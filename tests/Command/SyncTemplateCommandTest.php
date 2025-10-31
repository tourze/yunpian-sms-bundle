<?php

namespace YunpianSmsBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;
use YunpianSmsBundle\Command\SyncTemplateCommand;

/**
 * @internal
 */
#[CoversClass(SyncTemplateCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncTemplateCommandTest extends AbstractCommandTestCase
{
    protected function getCommandTester(): CommandTester
    {
        $command = self::getService(SyncTemplateCommand::class);
        self::assertInstanceOf(SyncTemplateCommand::class, $command);

        $application = new Application();
        $application->add($command);

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
    }

    public function testCommandConfiguration(): void
    {
        $command = self::getService(SyncTemplateCommand::class);
        self::assertInstanceOf(SyncTemplateCommand::class, $command);

        $this->assertEquals(SyncTemplateCommand::NAME, $command->getName());
        $this->assertEquals('同步云片短信模板', $command->getDescription());
    }
}
