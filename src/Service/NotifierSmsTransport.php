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

        $account = $this->getAccount();
        if (null === $account) {
            throw new RuntimeException('未找到可用的云片短信账号');
        }

        try {
            $request = $this->createSendRequest($message, $account);
            $response = $this->apiClient->requestArray($request);

            $this->validateResponse($response);

            return $this->createSentMessage($message, $response);
        } catch (TransportException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new RuntimeException(sprintf('发送短信时出现异常: %s', $e->getMessage()), 0, $e);
        }
    }

    private function createSendRequest(SmsMessage $message, Account $account): SendSmsRequest
    {
        $request = new SendSmsRequest();
        $request->setAccount($account);
        $request->setMobile($message->getPhone());
        $request->setContent($message->getSubject());

        $options = $message->getOptions();
        if ($options instanceof MessageOptionsInterface) {
            $request->setUid($options->getRecipientId());
        }

        return $request;
    }

    /**
     * @param array<string, mixed> $response
     */
    private function validateResponse(array $response): void
    {
        if (isset($response['code']) && 0 !== $response['code']) {
            $errorMsg = is_string($response['msg'] ?? null) ? $response['msg'] : '未知错误';
            throw new RuntimeException(sprintf('云片短信发送失败: %s', $errorMsg));
        }
    }

    /**
     * @param array<string, mixed> $response
     */
    private function createSentMessage(SmsMessage $message, array $response): SentMessage
    {
        $sentMessage = new SentMessage($message, (string) $this);

        if (isset($response['sid']) && (is_string($response['sid']) || is_int($response['sid']))) {
            $sentMessage->setMessageId((string) $response['sid']);
        }

        return $sentMessage;
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
