<?php

namespace YunpianSmsBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use YunpianSmsBundle\Service\SendLogService;

#[AsCommand(
    name: 'yunpian:sync-send-status',
    description: '同步云片短信发送状态'
)]
#[AsCronTask('*/5 * * * *')]
class SyncSendStatusCommand extends Command
{
    public function __construct(
        private readonly SendLogService $sendLogService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->sendLogService->syncStatus();
            $output->writeln('同步成功');
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $output->writeln(sprintf('同步失败: %s', $e->getMessage()));
            return Command::FAILURE;
        }
    }
}
