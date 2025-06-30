<?php

namespace YunpianSmsBundle\Tests\Unit\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\SendLog;
use YunpianSmsBundle\EventListener\SendLogListener;
use YunpianSmsBundle\Service\SendLogService;

class SendLogListenerTest extends TestCase
{
    public function testListenerCanBeInstantiated(): void
    {
        $sendLogService = $this->createMock(SendLogService::class);
        $logger = $this->createMock(LoggerInterface::class);

        $listener = new SendLogListener($sendLogService, $logger);
        $this->assertInstanceOf(SendLogListener::class, $listener);
    }

    public function testPrePersistWithSid(): void
    {
        $sendLogService = $this->createMock(SendLogService::class);
        $logger = $this->createMock(LoggerInterface::class);

        $sendLog = $this->createMock(SendLog::class);
        $sendLog->method('getSid')->willReturn('existing_sid');

        $sendLogService->expects($this->never())->method('send');
        $sendLogService->expects($this->never())->method('sendTpl');

        $listener = new SendLogListener($sendLogService, $logger);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $args = new \Doctrine\ORM\Event\PrePersistEventArgs($sendLog, $entityManager);

        $listener->prePersist($sendLog, $args);
    }

    public function testPrePersistWithoutSid(): void
    {
        $sendLogService = $this->createMock(SendLogService::class);
        $logger = $this->createMock(LoggerInterface::class);

        $account = $this->createMock(Account::class);
        $sendLog = $this->createMock(SendLog::class);
        $sendLog->method('getSid')->willReturn(null);
        $sendLog->method('getAccount')->willReturn($account);
        $sendLog->method('getTemplate')->willReturn(null);
        $sendLog->method('getMobile')->willReturn('13800138000');
        $sendLog->method('getContent')->willReturn('Test message');
        $sendLog->method('getUid')->willReturn('test_uid');

        $newSendLog = $this->createMock(SendLog::class);
        $newSendLog->method('getSid')->willReturn('new_sid');
        $newSendLog->method('getCount')->willReturn(1);
        $newSendLog->method('getFee')->willReturn('0.05');

        $sendLogService->expects($this->once())
            ->method('send')
            ->with($account, '13800138000', 'Test message', 'test_uid')
            ->willReturn($newSendLog);

        $sendLog->expects($this->once())->method('setSid')->with('new_sid');
        $sendLog->expects($this->once())->method('setCount')->with(1);
        $sendLog->expects($this->once())->method('setFee')->with('0.05');

        $listener = new SendLogListener($sendLogService, $logger);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $args = new \Doctrine\ORM\Event\PrePersistEventArgs($sendLog, $entityManager);

        $listener->prePersist($sendLog, $args);
    }
}