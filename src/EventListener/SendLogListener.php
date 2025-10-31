<?php

namespace YunpianSmsBundle\EventListener;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use YunpianSmsBundle\Entity\SendLog;
use YunpianSmsBundle\Enum\SendStatusEnum;
use YunpianSmsBundle\Exception\TemplateParseException;
use YunpianSmsBundle\Service\SendLogService;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: SendLog::class)]
#[WithMonologChannel(channel: 'yunpian_sms')]
class SendLogListener
{
    public function __construct(
        private readonly SendLogService $sendLogService,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function prePersist(SendLog $sendLog, PrePersistEventArgs $args): void
    {
        // 如果已经有 sid，说明是从 API 返回后设置的，不需要再次发送
        if (null !== $sendLog->getSid() && '' !== $sendLog->getSid()) {
            return;
        }

        try {
            // 根据是否有模板来决定发送方式
            if (null !== $sendLog->getTemplate()) {
                $newSendLog = $this->sendLogService->sendTpl(
                    $sendLog->getAccount(),
                    $sendLog->getTemplate(),
                    $sendLog->getMobile(),
                    $this->parseTplValue($sendLog->getContent()),
                    $sendLog->getUid(),
                );
            } else {
                $newSendLog = $this->sendLogService->send(
                    $sendLog->getAccount(),
                    $sendLog->getMobile(),
                    $sendLog->getContent(),
                    $sendLog->getUid(),
                );
            }

            // 复制发送结果
            $sendLog->setSid($newSendLog->getSid());
            $sendLog->setCount($newSendLog->getCount());
            $sendLog->setFee($newSendLog->getFee());
        } catch (\Throwable $e) {
            // 记录错误但不阻止实体保存
            $this->logger->error('短信发送失败: {message}', [
                'message' => $e->getMessage(),
                'exception' => $e,
                'account_id' => $sendLog->getAccount()->getId(),
                'mobile' => $sendLog->getMobile(),
                'template_id' => $sendLog->getTemplate()?->getId(),
                'content' => $sendLog->getContent(),
            ]);
            // 设置错误状态
            $sendLog->setStatus(SendStatusEnum::FAILED);
            $sendLog->setErrorMsg($e->getMessage());
        }
    }

    /**
     * 从内容中解析模板变量
     * 例如: 【云片】您的验证码是1234 => ['code' => '1234']
     */
    /**
     * @return array<string, string>
     */
    private function parseTplValue(string $content): array
    {
        $matches = [];
        if (0 === preg_match_all('/\{(\w+)\}(.*?)(?=\{|$)/u', $content, $matches, PREG_SET_ORDER)) {
            throw new TemplateParseException('无法从内容中解析模板变量');
        }

        $result = [];
        foreach ($matches as $match) {
            $key = $match[1];
            // 获取占位符后面的实际值
            $value = trim($match[2]);
            if ('' === $value) {
                throw new TemplateParseException(sprintf('模板变量 %s 没有对应的值', $key));
            }
            $result[$key] = $value;
        }

        return $result;
    }
}
