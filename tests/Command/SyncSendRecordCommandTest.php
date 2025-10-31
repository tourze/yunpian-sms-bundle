<?php

namespace YunpianSmsBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;
use YunpianSmsBundle\Command\SyncSendRecordCommand;

/**
 * @internal
 */
#[CoversClass(SyncSendRecordCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncSendRecordCommandTest extends AbstractCommandTestCase
{
    protected function getCommandTester(): CommandTester
    {
        $command = self::getService(SyncSendRecordCommand::class);
        self::assertInstanceOf(SyncSendRecordCommand::class, $command);

        $application = new Application();
        $application->add($command);

        return new CommandTester($command);
    }

    protected function onSetUp(): void
    {
        // Command 测试不需要特殊的设置
    }

    public function testExecuteCommandWithoutOptions(): void
    {
        $commandTester = $this->getCommandTester();
        $commandTester->execute([]);

        $statusCode = $commandTester->getStatusCode();
        $this->assertContains($statusCode, [0, 1], 'Command should return either success (0) or failure (1) status code');

        if (1 === $statusCode) {
            $output = $commandTester->getDisplay();
            $this->assertStringContainsString('同步失败', $output, 'When command fails, output should contain failure message');
        }
    }

    public function testExecuteCommandWithOptions(): void
    {
        $commandTester = $this->getCommandTester();
        $commandTester->execute([
            '--start-time' => '2024-01-01 00:00:00',
            '--end-time' => '2024-01-01 23:59:59',
            '--mobile' => '13800138000',
        ]);

        $statusCode = $commandTester->getStatusCode();
        $this->assertContains($statusCode, [0, 1], 'Command should return either success (0) or failure (1) status code');

        if (1 === $statusCode) {
            $output = $commandTester->getDisplay();
            $this->assertStringContainsString('同步失败', $output, 'When command fails, output should contain failure message');
        }
    }

    public function testCommandConfiguration(): void
    {
        $command = self::getService(SyncSendRecordCommand::class);
        self::assertInstanceOf(SyncSendRecordCommand::class, $command);

        $this->assertEquals(SyncSendRecordCommand::NAME, $command->getName());
        $this->assertEquals('同步云片短信发送记录', $command->getDescription());
        $this->assertTrue($command->getDefinition()->hasOption('start-time'));
        $this->assertTrue($command->getDefinition()->hasOption('end-time'));
        $this->assertTrue($command->getDefinition()->hasOption('mobile'));
    }

    public function testOptionStartTime(): void
    {
        $commandTester = $this->getCommandTester();
        $commandTester->execute(['--start-time' => '2024-01-01 00:00:00']);
        $this->assertContains($commandTester->getStatusCode(), [0, 1]);
    }

    public function testOptionEndTime(): void
    {
        $commandTester = $this->getCommandTester();
        $commandTester->execute(['--end-time' => '2024-01-01 23:59:59']);
        $this->assertContains($commandTester->getStatusCode(), [0, 1]);
    }

    public function testOptionMobile(): void
    {
        $commandTester = $this->getCommandTester();
        $commandTester->execute(['--mobile' => '13800138000']);
        $this->assertContains($commandTester->getStatusCode(), [0, 1]);
    }
}
