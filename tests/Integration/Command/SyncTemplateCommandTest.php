<?php

namespace YunpianSmsBundle\Tests\Integration\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use YunpianSmsBundle\Command\SyncTemplateCommand;
use YunpianSmsBundle\Repository\AccountRepository;
use YunpianSmsBundle\Service\TemplateService;

class SyncTemplateCommandTest extends TestCase
{
    public function testExecuteCommandSuccessfully(): void
    {
        $accountRepository = $this->createMock(AccountRepository::class);
        $templateService = $this->createMock(TemplateService::class);

        $accountRepository->expects($this->once())
            ->method('findAllValid')
            ->willReturn([]);

        $command = new SyncTemplateCommand($accountRepository, $templateService);
        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testCommandConfiguration(): void
    {
        $accountRepository = $this->createMock(AccountRepository::class);
        $templateService = $this->createMock(TemplateService::class);

        $command = new SyncTemplateCommand($accountRepository, $templateService);

        $this->assertEquals(SyncTemplateCommand::NAME, $command->getName());
        $this->assertEquals('同步云片短信模板', $command->getDescription());
    }
}