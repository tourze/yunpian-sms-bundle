<?php

namespace YunpianSmsBundle\Service;

use Symfony\Component\Notifier\Exception\RuntimeException;
use Symfony\Component\Notifier\Exception\TransportException;
use Symfony\Component\Notifier\Message\MessageInterface;
use Symfony\Component\Notifier\Message\MessageOptionsInterface;
use Symfony\Component\Notifier\Message\SentMessage;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\Transport\TransportInterface;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Repository\AccountRepository;
use YunpianSmsBundle\Request\SendSmsRequest;

/**
 * 特殊的Transport
 */
class NotifierSmsTransport implements TransportInterface
{
    public function __construct(
        private readonly SmsApiClient $apiClient,
        private readonly AccountRepository $accountRepository,
    ) {
    }

    public function __toString(): string
    {
        return 'yunpian_sms';
    }

    public function send(MessageInterface $message): ?SentMessage
    {
        if (!$message instanceof SmsMessage) {
            throw new RuntimeException('消息类型不支持，只支持 SmsMessage');
        }

        // 获取账号
        $account = $this->getAccount();
        if ($account === null) {
            throw new RuntimeException('未找到可用的云片短信账号');
        }

        try {
            // 创建发送请求
            $request = new SendSmsRequest();
            $request->setAccount($account);
            $request->setMobile($message->getPhone());
            $request->setContent($message->getSubject());

            // 如果消息有选项，提取 uid
            $options = $message->getOptions();
            if ($options instanceof MessageOptionsInterface) {
                $request->setUid($options->getRecipientId());
            }

            // 发送请求并获取HTTP响应
            $httpResponse = $this->apiClient->request($request);
            $response = $this->apiClient->requestArray($request);

            // 检查响应
            if (isset($response['code']) && $response['code'] !== 0) {
                $errorMsg = is_string($response['msg'] ?? null) ? $response['msg'] : '未知错误';
                throw new TransportException(sprintf('云片短信发送失败: %s', $errorMsg), $httpResponse);
            }

            // 构造 SentMessage
            $sentMessage = new SentMessage($message, (string) $this);
            if (isset($response['sid']) && (is_string($response['sid']) || is_int($response['sid']))) {
                $sentMessage->setMessageId((string) $response['sid']);
            }

            return $sentMessage;

        } catch (TransportException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new RuntimeException(sprintf('发送短信时出现异常: %s', $e->getMessage()), 0, $e);
        }
    }

    public function supports(MessageInterface $message): bool
    {
        return $message instanceof SmsMessage;
    }

    /**
     * 获取第一个有效的Account
     */
    private function getAccount(): ?Account
    {
        $validAccounts = $this->accountRepository->findAllValid();
        return $validAccounts[0] ?? null;
    }
}
