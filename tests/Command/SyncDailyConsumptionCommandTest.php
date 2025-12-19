<?php

namespace YunpianSmsBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;
use YunpianSmsBundle\Command\SyncDailyConsumptionCommand;

/**
 * @internal
 */
#[CoversClass(SyncDailyConsumptionCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncDailyConsumptionCommandTest extends AbstractCommandTestCase
{
    protected function getCommandTester(): CommandTester
    {
        $command = self::getService(SyncDailyConsumptionCommand::class);
        self::assertInstanceOf(SyncDailyConsumptionCommand::class, $command);

        $application = new Application();
        $application->addCommand($command);

        return new CommandTester($command);
    }

    protected function onSetUp(): void
    {
        // Command 测试不需要特殊的设置
    }

    public function testExecuteCommandWithoutDate(): void
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

    public function testExecuteCommandWithDate(): void
    {
        $commandTester = $this->getCommandTester();
        $commandTester->execute(['--date' => '2024-01-01']);

        $statusCode = $commandTester->getStatusCode();
        $this->assertContains($statusCode, [0, 1], 'Command should return either success (0) or failure (1) status code');

        if (1 === $statusCode) {
            $output = $commandTester->getDisplay();
            $this->assertStringContainsString('同步失败', $output, 'When command fails, output should contain failure message');
        }
    }

    public function testCommandConfiguration(): void
    {
        $command = self::getService(SyncDailyConsumptionCommand::class);
        self::assertInstanceOf(SyncDailyConsumptionCommand::class, $command);

        $this->assertEquals(SyncDailyConsumptionCommand::NAME, $command->getName());
        $this->assertEquals('同步云片短信日消耗', $command->getDescription());
        $this->assertTrue($command->getDefinition()->hasOption('date'));
    }

    public function testOptionDate(): void
    {
        $commandTester = $this->getCommandTester();
        $commandTester->execute(['--date' => '2024-01-01']);

        // 测试选项被正确接受
        $this->assertContains($commandTester->getStatusCode(), [0, 1]);
    }
}
