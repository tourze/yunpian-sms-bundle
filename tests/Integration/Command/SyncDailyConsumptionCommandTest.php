<?php

namespace YunpianSmsBundle\Tests\Integration\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use YunpianSmsBundle\Command\SyncDailyConsumptionCommand;
use YunpianSmsBundle\Repository\AccountRepository;
use YunpianSmsBundle\Service\DailyConsumptionService;

class SyncDailyConsumptionCommandTest extends TestCase
{
    public function testExecuteCommandWithoutDate(): void
    {
        $accountRepository = $this->createMock(AccountRepository::class);
        $dailyConsumptionService = $this->createMock(DailyConsumptionService::class);

        $accountRepository->expects($this->once())
            ->method('findAllValid')
            ->willReturn([]);

        $command = new SyncDailyConsumptionCommand($accountRepository, $dailyConsumptionService);
        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testExecuteCommandWithDate(): void
    {
        $accountRepository = $this->createMock(AccountRepository::class);
        $dailyConsumptionService = $this->createMock(DailyConsumptionService::class);

        $accountRepository->expects($this->once())
            ->method('findAllValid')
            ->willReturn([]);

        $command = new SyncDailyConsumptionCommand($accountRepository, $dailyConsumptionService);
        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute(['--date' => '2024-01-01']);

        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testCommandConfiguration(): void
    {
        $accountRepository = $this->createMock(AccountRepository::class);
        $dailyConsumptionService = $this->createMock(DailyConsumptionService::class);

        $command = new SyncDailyConsumptionCommand($accountRepository, $dailyConsumptionService);

        $this->assertEquals(SyncDailyConsumptionCommand::NAME, $command->getName());
        $this->assertEquals('同步云片短信日消耗', $command->getDescription());
        $this->assertTrue($command->getDefinition()->hasOption('date'));
    }
}