<?php

namespace YunpianSmsBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\Template;
use YunpianSmsBundle\Enum\NotifyTypeEnum;
use YunpianSmsBundle\Enum\TemplateTypeEnum;
use YunpianSmsBundle\Exception\InvalidTemplateException;
use YunpianSmsBundle\Repository\TemplateRepository;
use YunpianSmsBundle\Request\Template\AddTemplateRequest;
use YunpianSmsBundle\Request\Template\DeleteTemplateRequest;
use YunpianSmsBundle\Request\Template\GetTemplateRequest;
use YunpianSmsBundle\Request\Template\UpdateTemplateRequest;

#[WithMonologChannel(channel: 'yunpian_sms')]
class TemplateService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TemplateRepository $templateRepository,
        private readonly SmsApiClient $apiClient,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * 安全转换为字符串
     */
    private function toString(mixed $value, string $default = ''): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return $default;
    }

    /**
     * 验证数组是否为 array<string, mixed> 类型
     *
     * @param mixed $value
     *
     * @return array<string, mixed>
     */
    private function ensureStringKeyedArray(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        // 检查所有的键是否都是字符串
        foreach (array_keys($value) as $key) {
            if (!is_string($key)) {
                // 如果有非字符串键，转换为字符串键数组
                $stringKeyedArray = [];
                foreach ($value as $k => $v) {
                    $stringKeyedArray[(string) $k] = $v;
                }

                return $stringKeyedArray;
            }
        }

        /** @var array<string, mixed> $value */
        return $value;
    }

    /**
     * 同步模板
     *
     * @return Template[]
     */
    public function syncTemplates(Account $account): array
    {
        try {
            // 获取远程模板列表
            $request = new GetTemplateRequest();
            $request->setAccount($account);
            $response = $this->apiClient->requestArray($request);

            $result = [];

            foreach ($response as $tpl) {
                if (!is_array($tpl)) {
                    continue;
                }
                $typedTpl = $this->ensureStringKeyedArray($tpl);
                $template = $this->processSingleTemplate($account, $typedTpl);
                if (null !== $template) {
                    $result[] = $template;
                }
            }

            $this->entityManager->flush();

            return $result;
        } catch (\Throwable $e) {
            $this->logger->error('同步模板失败: {message}', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return [];
        }
    }

    /**
     * 处理单个模板数据
     *
     * @param array<string, mixed> $tpl
     */
    private function processSingleTemplate(Account $account, array $tpl): ?Template
    {
        $tplId = $tpl['tpl_id'] ?? null;
        if (!is_string($tplId) && !is_int($tplId)) {
            return null;
        }

        $tplIdString = $this->toString($tplId);

        $template = $this->templateRepository->findOneBy([
            'account' => $account,
            'tplId' => $tplIdString,
        ]);

        if (null === $template) {
            $template = $this->createNewTemplate($account, $tplIdString);
        }

        $this->updateTemplateFromData($template, $tpl);

        return $template;
    }

    private function createNewTemplate(Account $account, string $tplId): Template
    {
        $template = new Template();
        $template->setAccount($account);
        $template->setTplId($tplId);
        $template->setNotifyType(NotifyTypeEnum::ALWAYS);
        $template->setTemplateType(TemplateTypeEnum::NOTIFICATION);
        $this->entityManager->persist($template);

        return $template;
    }

    /**
     * @param array<string, mixed> $tpl
     */
    private function updateTemplateFromData(Template $template, array $tpl): void
    {
        $tplTitle = $tpl['tpl_title'] ?? '';
        $tplContent = $tpl['tpl_content'] ?? '';
        $checkStatus = $tpl['check_status'] ?? '';
        $reason = $tpl['reason'] ?? null;

        $template->setTitle($this->toString($tplTitle));
        $template->setContent($this->toString($tplContent));
        $template->setCheckStatus($this->toString($checkStatus));
        $template->setCheckReply(is_string($reason) ? $reason : null);
    }

    public function createTemplate(Template $template): void
    {
        if ($template->getTemplateType()->isVerification() && (null === $template->getWebsite() || '' === $template->getWebsite() || null === $template->getApplyDescription() || '' === $template->getApplyDescription())) {
            throw new InvalidTemplateException('验证码类模板必须提供网站地址和说明');
        }

        $request = new AddTemplateRequest();
        $request->setAccount($template->getAccount());
        $request->setContent($template->getContent());
        $request->setNotifyType($template->getNotifyType());
        $request->setWebsite($template->getWebsite());
        $request->setTemplateType($template->getTemplateType());
        $request->setCallback($template->getCallback());
        $request->setApplyDescription($template->getApplyDescription());

        //        $response = $this->apiClient->requestArray($request);
        //        $template->setTplId($response['tpl_id'] ?? null);
    }

    /**
     * 创建模板
     */
    public function create(Account $account, string $tplContent, bool $notify = true): Template
    {
        try {
            $request = new AddTemplateRequest();
            $request->setAccount($account);
            $request->setContent($tplContent);
            $request->setNotifyType($notify ? NotifyTypeEnum::ALWAYS : NotifyTypeEnum::ONLY_FAILED);

            $response = $this->apiClient->requestArray($request);

            $tplId = $response['tpl_id'] ?? null;
            if (!is_string($tplId) && !is_int($tplId)) {
                throw new \InvalidArgumentException('Invalid template ID received from API');
            }

            $template = new Template();
            $template->setAccount($account);
            $template->setTplId($this->toString($tplId));
            $template->setTitle('自动创建模板'); // 添加默认标题
            $template->setContent($tplContent);
            $template->setNotifyType($notify ? NotifyTypeEnum::ALWAYS : NotifyTypeEnum::ONLY_FAILED);
            $template->setTemplateType(TemplateTypeEnum::NOTIFICATION);
            $template->setCheckStatus('SUCCESS');

            $this->entityManager->persist($template);
            $this->entityManager->flush();

            return $template;
        } catch (\Throwable $e) {
            $this->logger->error('创建模板失败: {message}', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * 更新模板
     */
    public function update(Template $template, string $newContent): Template
    {
        try {
            $request = new UpdateTemplateRequest();
            $request->setAccount($template->getAccount());
            $request->setTplId($template->getTplId());
            $request->setContent($newContent);

            $this->apiClient->requestArray($request);

            $template->setContent($newContent);
            $this->entityManager->flush();

            return $template;
        } catch (\Throwable $e) {
            $this->logger->error('更新模板失败: {message}', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * 删除模板
     */
    public function delete(Template $template): bool
    {
        try {
            $request = new DeleteTemplateRequest();
            $request->setAccount($template->getAccount());
            $request->setTemplateId($template->getTplId());

            $this->apiClient->requestArray($request);
            $this->entityManager->remove($template);
            $this->entityManager->flush();

            return true;
        } catch (\Throwable $e) {
            $this->logger->error('删除模板失败: {message}', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return false;
        }
    }

    /**
     * @return Template[]
     */
    public function findByAccount(Account $account): array
    {
        return $this->templateRepository->findByAccount($account);
    }

    public function findOneByAccountAndTplId(Account $account, string $tplId): ?Template
    {
        return $this->templateRepository->findOneByAccountAndTplId($account, $tplId);
    }
}
