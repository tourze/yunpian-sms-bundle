<?php

namespace YunpianSmsBundle\Tests\Integration\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use YunpianSmsBundle\Command\SyncSendRecordCommand;
use YunpianSmsBundle\Repository\AccountRepository;
use YunpianSmsBundle\Repository\SendLogRepository;
use YunpianSmsBundle\Service\SendLogService;

class SyncSendRecordCommandTest extends TestCase
{
    public function testExecuteCommandWithoutOptions(): void
    {
        $accountRepository = $this->createMock(AccountRepository::class);
        $sendLogRepository = $this->createMock(SendLogRepository::class);
        $sendLogService = $this->createMock(SendLogService::class);

        $accountRepository->expects($this->once())
            ->method('findAllValid')
            ->willReturn([]);

        $sendLogRepository->expects($this->once())
            ->method('findLastSendTime')
            ->willReturn(null);

        $command = new SyncSendRecordCommand($accountRepository, $sendLogRepository, $sendLogService);
        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testExecuteCommandWithOptions(): void
    {
        $accountRepository = $this->createMock(AccountRepository::class);
        $sendLogRepository = $this->createMock(SendLogRepository::class);
        $sendLogService = $this->createMock(SendLogService::class);

        $accountRepository->expects($this->once())
            ->method('findAllValid')
            ->willReturn([]);

        $command = new SyncSendRecordCommand($accountRepository, $sendLogRepository, $sendLogService);
        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--start-time' => '2024-01-01 00:00:00',
            '--end-time' => '2024-01-01 23:59:59',
            '--mobile' => '13800138000'
        ]);

        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testCommandConfiguration(): void
    {
        $accountRepository = $this->createMock(AccountRepository::class);
        $sendLogRepository = $this->createMock(SendLogRepository::class);
        $sendLogService = $this->createMock(SendLogService::class);

        $command = new SyncSendRecordCommand($accountRepository, $sendLogRepository, $sendLogService);

        $this->assertEquals(SyncSendRecordCommand::NAME, $command->getName());
        $this->assertEquals('同步云片短信发送记录', $command->getDescription());
        $this->assertTrue($command->getDefinition()->hasOption('start-time'));
        $this->assertTrue($command->getDefinition()->hasOption('end-time'));
        $this->assertTrue($command->getDefinition()->hasOption('mobile'));
    }
}