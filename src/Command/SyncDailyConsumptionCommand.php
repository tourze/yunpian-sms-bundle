<?php

namespace YunpianSmsBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use YunpianSmsBundle\Repository\AccountRepository;
use YunpianSmsBundle\Service\DailyConsumptionService;

#[AsCommand(
    name: 'yunpian:sync-daily-consumption',
    description: '同步云片短信日消耗'
)]
#[AsCronTask('15 */4 * * *')]
class SyncDailyConsumptionCommand extends Command
{
    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly DailyConsumptionService $dailyConsumptionService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('date', 'd', InputOption::VALUE_OPTIONAL, '同步指定日期的数据(Y-m-d)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $date = $input->getOption('date')
            ? new \DateTime($input->getOption('date'))
            : new \DateTime('yesterday');

        $accounts = $this->accountRepository->findAllValid();

        foreach ($accounts as $account) {
            $output->writeln(sprintf('正在同步账号 %s 的消耗数据...', $account->getRemark() ?? $account->getApiKey()));

            try {
                $this->dailyConsumptionService->syncDailyConsumption($account, $date);
                $output->writeln('同步成功');
            } catch (\Exception $e) {
                $output->writeln(sprintf('同步失败: %s', $e->getMessage()));
                return Command::FAILURE;
            }
        }

        return Command::SUCCESS;
    }
}
