<?php

namespace YunpianSmsBundle\Tests\Integration\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use YunpianSmsBundle\Command\SyncSignCommand;
use YunpianSmsBundle\Repository\AccountRepository;
use YunpianSmsBundle\Service\SignService;

class SyncSignCommandTest extends TestCase
{
    public function testExecuteCommandSuccessfully(): void
    {
        $accountRepository = $this->createMock(AccountRepository::class);
        $signService = $this->createMock(SignService::class);

        $accountRepository->expects($this->once())
            ->method('findAllValid')
            ->willReturn([]);

        $command = new SyncSignCommand($accountRepository, $signService);
        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testCommandConfiguration(): void
    {
        $accountRepository = $this->createMock(AccountRepository::class);
        $signService = $this->createMock(SignService::class);

        $command = new SyncSignCommand($accountRepository, $signService);

        $this->assertEquals(SyncSignCommand::NAME, $command->getName());
        $this->assertEquals('同步云片短信签名', $command->getDescription());
    }
}