<?php

namespace YunpianSmsBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Notifier\Exception\RuntimeException;
use Symfony\Component\Notifier\Message\MessageInterface;
use Symfony\Component\Notifier\Message\SentMessage;
use Symfony\Component\Notifier\Message\SmsMessage;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Repository\AccountRepository;
use YunpianSmsBundle\Service\NotifierSmsTransport;
use YunpianSmsBundle\Service\SmsApiClient;
use YunpianSmsBundle\Tests\Mock\MockHelper;

/**
 * @internal
 */
#[CoversClass(NotifierSmsTransport::class)]
final class NotifierSmsTransportTest extends TestCase
{
    public function testSendSmsMessage(): void
    {
        $apiClient = $this->createMock(SmsApiClient::class);
        $accountRepository = $this->createMock(AccountRepository::class);

        $transport = new NotifierSmsTransport($apiClient, $accountRepository);

        $account = MockHelper::createAccount();
        $accountRepository->method('findAllValid')->willReturn([$account]);

        $apiClient->method('requestArray')->willReturn([
            'sid' => 'test-sid-123',
            'count' => 1,
            'fee' => '0.050',
            'code' => 0,
            'msg' => '发送成功',
        ]);

        $message = new SmsMessage('+8613800138000', '测试短信内容');
        $sentMessage = $transport->send($message);

        $this->assertInstanceOf(SentMessage::class, $sentMessage);
        $this->assertEquals($message, $sentMessage->getOriginalMessage());
        $this->assertEquals('test-sid-123', $sentMessage->getMessageId());
    }

    public function testToString(): void
    {
        $apiClient = $this->createMock(SmsApiClient::class);
        $accountRepository = $this->createMock(AccountRepository::class);

        $transport = new NotifierSmsTransport($apiClient, $accountRepository);

        $this->assertEquals('yunpian_sms', (string) $transport);
    }

    public function testSendThrowsExceptionForInvalidMessageType(): void
    {
        $apiClient = $this->createMock(SmsApiClient::class);
        $accountRepository = $this->createMock(AccountRepository::class);

        $transport = new NotifierSmsTransport($apiClient, $accountRepository);

        $invalidMessage = $this->createMock(MessageInterface::class);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('消息类型不支持，只支持 SmsMessage');

        $transport->send($invalidMessage);
    }

    public function testSendThrowsExceptionWhenNoAccountFound(): void
    {
        $apiClient = $this->createMock(SmsApiClient::class);
        $accountRepository = $this->createMock(AccountRepository::class);

        $transport = new NotifierSmsTransport($apiClient, $accountRepository);
        $accountRepository->method('findAllValid')->willReturn([]);

        $message = new SmsMessage('+8613800138000', '测试短信内容');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('未找到可用的云片短信账号');

        $transport->send($message);
    }

    public function testSupportsReturnsTrueForSmsMessage(): void
    {
        $apiClient = $this->createMock(SmsApiClient::class);
        $accountRepository = $this->createMock(AccountRepository::class);

        $transport = new NotifierSmsTransport($apiClient, $accountRepository);

        $smsMessage = new SmsMessage('+8613800138000', '测试短信内容');
        $this->assertTrue($transport->supports($smsMessage));
    }

    public function testSupportsReturnsFalseForNonSmsMessage(): void
    {
        $apiClient = $this->createMock(SmsApiClient::class);
        $accountRepository = $this->createMock(AccountRepository::class);

        $transport = new NotifierSmsTransport($apiClient, $accountRepository);

        $nonSmsMessage = $this->createMock(MessageInterface::class);
        $this->assertFalse($transport->supports($nonSmsMessage));
    }
}
