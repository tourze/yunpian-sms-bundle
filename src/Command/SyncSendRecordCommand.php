<?php

namespace YunpianSmsBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use YunpianSmsBundle\Repository\AccountRepository;
use YunpianSmsBundle\Repository\SendLogRepository;
use YunpianSmsBundle\Service\SendLogService;

#[AsCommand(
    name: 'yunpian:sync-send-record',
    description: '同步云片短信发送记录'
)]
#[AsCronTask('0 */4 * * *')]
class SyncSendRecordCommand extends Command
{
    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly SendLogRepository $sendLogRepository,
        private readonly SendLogService $sendLogService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('start-time', 's', InputOption::VALUE_OPTIONAL, '开始时间(Y-m-d H:i:s)');
        $this->addOption('end-time', 'e', InputOption::VALUE_OPTIONAL, '结束时间(Y-m-d H:i:s)');
        $this->addOption('mobile', 'm', InputOption::VALUE_OPTIONAL, '手机号');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $startTime = $input->getOption('start-time')
            ? new \DateTime($input->getOption('start-time'))
            : $this->getDefaultStartTime();

        $endTime = $input->getOption('end-time')
            ? new \DateTime($input->getOption('end-time'))
            : new \DateTime();

        $mobile = $input->getOption('mobile');

        $accounts = $this->accountRepository->findAllValid();

        foreach ($accounts as $account) {
            $output->writeln(sprintf('正在同步账号 %s 的发送记录...', $account->getRemark() ?? $account->getApiKey()));

            try {
                $this->sendLogService->syncRecord($account, $startTime, $endTime, $mobile);
                $output->writeln('同步成功');
            } catch (\Exception $e) {
                $output->writeln(sprintf('同步失败: %s', $e->getMessage()));
                return Command::FAILURE;
            }
        }

        return Command::SUCCESS;
    }

    private function getDefaultStartTime(): \DateTime
    {
        $lastSendTime = $this->sendLogRepository->findLastSendTime();
        if ($lastSendTime) {
            return $lastSendTime;
        }

        // 如果没有记录，默认同步最近24小时的数据
        return new \DateTime('-24 hours');
    }
}
