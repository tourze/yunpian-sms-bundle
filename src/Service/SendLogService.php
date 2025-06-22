<?php

namespace YunpianSmsBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\SendLog;
use YunpianSmsBundle\Entity\Template;
use YunpianSmsBundle\Repository\SendLogRepository;
use YunpianSmsBundle\Request\GetSendRecordRequest;
use YunpianSmsBundle\Request\GetSendStatusRequest;
use YunpianSmsBundle\Request\SendSmsRequest;
use YunpianSmsBundle\Request\SendTplSmsRequest;

class SendLogService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SendLogRepository $sendLogRepository,
        private readonly SmsApiClient $apiClient,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function send(Account $account, string $mobile, string $content, ?string $uid = null): SendLog
    {
        $request = new SendSmsRequest();
        $request->setAccount($account);
        $request->setMobile($mobile);
        $request->setContent($content);
        $request->setUid($uid);

        $response = $this->apiClient->request($request);

        $sendLog = new SendLog();
        $sendLog->setAccount($account);
        $sendLog->setMobile($mobile);
        $sendLog->setContent($content);
        $sendLog->setUid($uid);
        $sendLog->setSid($response['sid'] ?? null);
        $sendLog->setCount($response['count'] ?? 0);
        $sendLog->setFee($response['fee'] ?? '0.000');

        return $sendLog;
    }

    public function sendTpl(Account $account, Template $template, string $mobile, array $tplValue, ?string $uid = null): SendLog
    {
        $request = new SendTplSmsRequest();
        $request->setAccount($account);
        $request->setMobile($mobile);
        $request->setTplId($template->getTplId());
        $request->setTplValue($tplValue);
        $request->setUid($uid);

        $response = $this->apiClient->request($request);

        $sendLog = new SendLog();
        $sendLog->setAccount($account);
        $sendLog->setTemplate($template);
        $sendLog->setMobile($mobile);
        $sendLog->setContent($response['text'] ?? '');
        $sendLog->setUid($uid);
        $sendLog->setSid($response['sid'] ?? null);
        $sendLog->setCount($response['count'] ?? 0);
        $sendLog->setFee($response['fee'] ?? '0.000');

        return $sendLog;
    }

    public function create(Account $account, string $mobile, string $content, ?Template $template = null, ?string $uid = null): SendLog
    {
        $sendLog = new SendLog();
        $sendLog->setAccount($account);
        $sendLog->setTemplate($template);
        $sendLog->setMobile($mobile);
        $sendLog->setContent($content);
        $sendLog->setUid($uid);

        $this->entityManager->persist($sendLog);
        $this->entityManager->flush();

        return $sendLog;
    }

    public function syncStatus(): void
    {
        $sendLogs = $this->sendLogRepository->findPendingStatus();
        if (empty($sendLogs)) {
            return;
        }

        // 按账号分组
        /** @var SendLog[][] $groups */
        $groups = [];
        foreach ($sendLogs as $sendLog) {
            if ($sendLog->getSid() !== null) { // 只处理有 sid 的记录
                $groups[$sendLog->getAccount()->getId()][] = $sendLog;
            }
        }

        foreach ($groups as $groupedSendLogs) {
            $account = $groupedSendLogs[0]->getAccount();
            $sids = array_filter(array_map(fn(SendLog $sendLog) => $sendLog->getSid(), $groupedSendLogs));
            if (empty($sids)) {
                continue;
            }

            try {
                $request = new GetSendStatusRequest();
                $request->setAccount($account);
                $request->setSids($sids);

                $response = $this->apiClient->request($request);
                
                // 用 Map 优化查找
                $sendLogMap = [];
                foreach ($groupedSendLogs as $sendLog) {
                    if ($sendLog->getSid() !== null) {
                        $sendLogMap[$sendLog->getSid()] = $sendLog;
                    }
                }

                foreach ($response as $status) {
                    $sid = $status['sid'] ?? null;
                    if ($sid && isset($sendLogMap[$sid])) {
                        $sendLog = $sendLogMap[$sid];
                        $sendLog->setStatus($status['status'] ?? null);
                        $sendLog->setStatusMsg($status['status_msg'] ?? null);
                        $sendLog->setReceiveTime(isset($status['user_receive_time']) ? new \DateTimeImmutable($status['user_receive_time']) : null);
                        $sendLog->setErrorMsg($status['error_msg'] ?? null);
                    }
                }

                $this->entityManager->flush();
            } catch (\Throwable $e) {
                // 记录错误日志但继续处理其他组
                $this->logger->error('同步发送状态失败: {message}', [
                    'message' => $e->getMessage(),
                    'exception' => $e,
                    'account_id' => $account->getId(),
                    'sids' => $sids,
                ]);
                continue;
            }
        }
    }

    public function syncRecord(Account $account, \DateTimeInterface $startTime, \DateTimeInterface $endTime, ?string $mobile = null): void
    {
        $pageNum = 1;
        $pageSize = 100;
        $count = 0;

        while (true) {
            try {
                $request = new GetSendRecordRequest();
                $request->setAccount($account);
                $request->setStartTime($startTime);
                $request->setEndTime($endTime);
                $request->setMobile($mobile);
                $request->setPageNum((string)$pageNum);
                $request->setPageSize((string)$pageSize);

                $response = $this->apiClient->request($request);
                if (empty($response)) {
                    break;
                }

                foreach ($response as $record) {
                    $sid = $record['sid'] ?? null;
                    if (!$sid) {
                        continue;
                    }

                    $sendLog = $this->sendLogRepository->findOneBySid($sid);
                    if ($sendLog === null) {
                        $sendLog = new SendLog();
                        $sendLog->setAccount($account);
                        $sendLog->setSid($sid);
                        $this->entityManager->persist($sendLog);
                    }

                    $sendLog->setMobile($record['mobile']);
                    $sendLog->setContent($record['text']);
                    $sendLog->setCount($record['count'] ?? 0);
                    $sendLog->setFee($record['fee'] ?? '0.000');
                    $sendLog->setUid($record['uid'] ?? null);
                    $sendLog->setStatus($record['status'] ?? null);
                    $sendLog->setStatusMsg($record['status_msg'] ?? null);
                    $sendLog->setReceiveTime(isset($record['user_receive_time']) ? new \DateTimeImmutable($record['user_receive_time']) : null);
                    $sendLog->setErrorMsg($record['error_msg'] ?? null);

                    if (++$count % $pageSize === 0) {
                        $this->entityManager->flush();
                        $this->entityManager->clear(); // 清理内存
                    }
                }

                if (count($response) < $pageSize) {
                    break;
                }

                $pageNum++;
            } catch (\Throwable $e) {
                $this->logger->error('同步发送记录失败: {message}', [
                    'message' => $e->getMessage(),
                    'exception' => $e,
                    'account_id' => $account->getId(),
                    'start_time' => $startTime->format('Y-m-d H:i:s'),
                    'end_time' => $endTime->format('Y-m-d H:i:s'),
                    'mobile' => $mobile,
                ]);
                throw $e; // 重新抛出异常,因为这是主动调用的同步
            }
        }

        // 最后一批
        if ($count % $pageSize !== 0) {
            $this->entityManager->flush();
        }
    }
}
