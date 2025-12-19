<?php

namespace YunpianSmsBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;
use YunpianSmsBundle\Command\SyncSendStatusCommand;

/**
 * @internal
 */
#[CoversClass(SyncSendStatusCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncSendStatusCommandTest extends AbstractCommandTestCase
{
    protected function getCommandTester(): CommandTester
    {
        $command = self::getService(SyncSendStatusCommand::class);
        self::assertInstanceOf(SyncSendStatusCommand::class, $command);

        $application = new Application();
        $application->addCommand($command);

        return new CommandTester($command);
    }

    protected function onSetUp(): void
    {
        // Command 测试不需要特殊的设置
    }

    public function testExecuteCommandSuccessfully(): void
    {
        $commandTester = $this->getCommandTester();
        $commandTester->execute([]);

        $this->assertEquals(0, $commandTester->getStatusCode());
        $this->assertStringContainsString('同步成功', $commandTester->getDisplay());
    }

    public function testExecuteCommandWithException(): void
    {
        $commandTester = $this->getCommandTester();
        $commandTester->execute([]);

        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testCommandConfiguration(): void
    {
        $command = self::getService(SyncSendStatusCommand::class);
        self::assertInstanceOf(SyncSendStatusCommand::class, $command);

        $this->assertEquals(SyncSendStatusCommand::NAME, $command->getName());
        $this->assertEquals('同步云片短信发送状态', $command->getDescription());
    }
}
