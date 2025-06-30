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
    name: self::NAME,
    description: '同步云片短信发送记录'
)]
#[AsCronTask(expression: '0 */4 * * *')]
class SyncSendRecordCommand extends Command
{
    public const NAME = 'yunpian:sync-send-record';
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
        $startTimeOption = $input->getOption('start-time');
        $startTime = is_string($startTimeOption) && $startTimeOption !== ''
            ? new \DateTime($startTimeOption)
            : $this->getDefaultStartTime();

        $endTimeOption = $input->getOption('end-time');
        $endTime = is_string($endTimeOption) && $endTimeOption !== ''
            ? new \DateTime($endTimeOption)
            : new \DateTime();

        $mobileOption = $input->getOption('mobile');
        $mobile = is_string($mobileOption) ? $mobileOption : null;

        $accounts = $this->accountRepository->findAllValid();

        foreach ($accounts as $account) {
            $output->writeln(sprintf('正在同步账号 %s 的发送记录...', $account->getRemark() ?? $account->getApiKey()));

            try {
                $this->sendLogService->syncRecord($account, $startTime, $endTime, $mobile);
                $output->writeln('同步成功');
            } catch (\Throwable $e) {
                $output->writeln(sprintf('同步失败: %s', $e->getMessage()));
                return Command::FAILURE;
            }
        }

        return Command::SUCCESS;
    }

    private function getDefaultStartTime(): \DateTime
    {
        $lastSendTime = $this->sendLogRepository->findLastSendTime();
        if ($lastSendTime !== null) {
            return $lastSendTime instanceof \DateTime ? $lastSendTime : new \DateTime($lastSendTime->format('Y-m-d H:i:s'));
        }

        // 如果没有记录，默认同步最近24小时的数据
        return new \DateTime('-24 hours');
    }
}
