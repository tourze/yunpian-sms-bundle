<?php

namespace YunpianSmsBundle\Tests\Integration\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use YunpianSmsBundle\Command\SyncSendStatusCommand;
use YunpianSmsBundle\Service\SendLogService;

class SyncSendStatusCommandTest extends TestCase
{
    public function testExecuteCommandSuccessfully(): void
    {
        $sendLogService = $this->createMock(SendLogService::class);
        $sendLogService->expects($this->once())
            ->method('syncStatus');

        $command = new SyncSendStatusCommand($sendLogService);
        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $this->assertEquals(0, $commandTester->getStatusCode());
        $this->assertStringContainsString('同步成功', $commandTester->getDisplay());
    }

    public function testExecuteCommandWithException(): void
    {
        $sendLogService = $this->createMock(SendLogService::class);
        $sendLogService->expects($this->once())
            ->method('syncStatus')
            ->willThrowException(new \Exception('Test exception'));

        $command = new SyncSendStatusCommand($sendLogService);
        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $this->assertEquals(1, $commandTester->getStatusCode());
        $this->assertStringContainsString('同步失败: Test exception', $commandTester->getDisplay());
    }

    public function testCommandConfiguration(): void
    {
        $sendLogService = $this->createMock(SendLogService::class);
        $command = new SyncSendStatusCommand($sendLogService);

        $this->assertEquals(SyncSendStatusCommand::NAME, $command->getName());
        $this->assertEquals('同步云片短信发送状态', $command->getDescription());
    }
}