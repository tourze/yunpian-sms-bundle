<?php

namespace YunpianSmsBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Notifier\Exception\RuntimeException;
use Symfony\Component\Notifier\Message\MessageInterface;
use Symfony\Component\Notifier\Message\SentMessage;
use Symfony\Component\Notifier\Message\SmsMessage;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Service\NotifierSmsTransport;

/**
 * @internal
 */
#[CoversClass(NotifierSmsTransport::class)]
#[RunTestsInSeparateProcesses]
final class NotifierSmsTransportTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testSendSmsMessage(): void
    {
        $transport = self::getService(NotifierSmsTransport::class);
        $entityManager = self::getService(EntityManagerInterface::class);

        // 创建一个测试账户
        $account = new Account();
        $account->setApiKey('test-api-key');
        $account->setValid(true);
        $account->setRemark('Test Account for Transport');
        $entityManager->persist($account);
        $entityManager->flush();

        // 创建短信消息
        $smsMessage = new SmsMessage('13800138000', 'Test message from NotifierSmsTransport');

        // 发送消息
        $sentMessage = $transport->send($smsMessage);

        // 验证返回的消息
        $this->assertInstanceOf(SentMessage::class, $sentMessage);
    }

    public function testSendThrowsExceptionForInvalidMessageType(): void
    {
        $transport = self::getService(NotifierSmsTransport::class);

        // 创建一个非SMS消息
        $invalidMessage = new class implements MessageInterface {
            public function getRecipientId(): string
            {
                return 'invalid@example.com';
            }

            public function getSubject(): string
            {
                return 'Test Subject';
            }

            public function getContent(): string
            {
                return 'Test Content';
            }

            public function getOptions(): ?\Symfony\Component\Notifier\Message\MessageOptionsInterface
            {
                return null;
            }

            public function getTransport(): ?string
            {
                return null;
            }
        };

        // 期望抛出异常
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('消息类型不支持，只支持 SmsMessage');

        $transport->send($invalidMessage);
    }

    public function testSendThrowsExceptionWhenNoAccountFound(): void
    {
        $transport = self::getService(NotifierSmsTransport::class);
        $accountRepository = self::getService(\YunpianSmsBundle\Repository\AccountRepository::class);

        // 删除所有现有账户
        $accounts = $accountRepository->findAll();
        foreach ($accounts as $account) {
            $accountRepository->remove($account, true);
        }

        // 创建短信消息，但没有有效账户
        $smsMessage = new SmsMessage('13900139000', 'Test message without account');

        // 期望抛出异常，因为没有有效的账户
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('未找到可用的云片短信账号');

        $transport->send($smsMessage);
    }

    public function testSupportsReturnsTrueForSmsMessage(): void
    {
        $transport = self::getService(NotifierSmsTransport::class);
        $smsMessage = new SmsMessage('13800138000', 'Test message');

        $this->assertTrue($transport->supports($smsMessage));
    }

    public function testSupportsReturnsFalseForNonSmsMessage(): void
    {
        $transport = self::getService(NotifierSmsTransport::class);
        $invalidMessage = new class implements MessageInterface {
            public function getRecipientId(): string
            {
                return 'invalid@example.com';
            }

            public function getSubject(): string
            {
                return 'Test Subject';
            }

            public function getContent(): string
            {
                return 'Test Content';
            }

            public function getOptions(): ?\Symfony\Component\Notifier\Message\MessageOptionsInterface
            {
                return null;
            }

            public function getTransport(): ?string
            {
                return null;
            }
        };

        $this->assertFalse($transport->supports($invalidMessage));
    }

    public function testToString(): void
    {
        $transport = self::getService(NotifierSmsTransport::class);
        $this->assertSame('yunpian_sms', $transport->__toString());
    }
}