<?php

namespace YunpianSmsBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use YunpianSmsBundle\Repository\AccountRepository;
use YunpianSmsBundle\Service\SignService;

#[AsCommand(
    name: self::NAME,
    description: '同步云片短信签名'
)]
class SyncSignCommand extends Command
{
    public const NAME = 'yunpian:sync-sign';

    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly SignService $signService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $accounts = $this->accountRepository->findAllValid();

        foreach ($accounts as $account) {
            $output->writeln(sprintf('正在同步账号 %s 的签名...', $account->getRemark() ?? $account->getApiKey()));

            try {
                $this->signService->syncSigns($account);
                $output->writeln('同步成功');
            } catch (\Throwable $e) {
                $output->writeln(sprintf('同步失败: %s', $e->getMessage()));

                return Command::FAILURE;
            }
        }

        return Command::SUCCESS;
    }
}
