<?php

namespace YunpianSmsBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\SendLog;
use YunpianSmsBundle\Entity\Template;
use YunpianSmsBundle\Repository\SendLogRepository;
use YunpianSmsBundle\Request\GetSendRecordRequest;
use YunpianSmsBundle\Request\GetSendStatusRequest;
use YunpianSmsBundle\Request\SendSmsRequest;
use YunpianSmsBundle\Request\SendTplSmsRequest;

#[WithMonologChannel(channel: 'yunpian_sms')]
class SendLogService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SendLogRepository $sendLogRepository,
        private readonly SmsApiClient $apiClient,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * 安全转换为整数
     */
    private function toInt(mixed $value, int $default = 0): int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        return $default;
    }

    /**
     * 安全转换为字符串
     */
    private function toString(mixed $value, string $default = ''): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return $default;
    }

    /**
     * 验证数组是否为 array<string, mixed> 类型
     *
     * @param mixed $value
     *
     * @return array<string, mixed>
     */
    private function ensureStringKeyedArray(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        // 检查所有的键是否都是字符串
        foreach (array_keys($value) as $key) {
            if (!is_string($key)) {
                // 如果有非字符串键，转换为字符串键数组
                $stringKeyedArray = [];
                foreach ($value as $k => $v) {
                    $stringKeyedArray[(string) $k] = $v;
                }

                return $stringKeyedArray;
            }
        }

        /** @var array<string, mixed> $value */
        return $value;
    }

    public function send(Account $account, string $mobile, string $content, ?string $uid = null): SendLog
    {
        $request = new SendSmsRequest();
        $request->setAccount($account);
        $request->setMobile($mobile);
        $request->setContent($content);
        $request->setUid($uid);

        $response = $this->apiClient->requestArray($request);

        $sendLog = new SendLog();
        $sendLog->setAccount($account);
        $sendLog->setMobile($mobile);
        $sendLog->setContent($content);
        $sendLog->setUid($uid);

        $sid = $response['sid'] ?? null;
        $count = $response['count'] ?? 0;
        $fee = $response['fee'] ?? '0.000';

        $sendLog->setSid(is_string($sid) ? $sid : null);
        $sendLog->setCount($this->toInt($count));
        $sendLog->setFee($this->toString($fee, '0.000'));

        return $sendLog;
    }

    /**
     * @param array<string, mixed> $tplValue
     */
    public function sendTpl(Account $account, Template $template, string $mobile, array $tplValue, ?string $uid = null): SendLog
    {
        $request = new SendTplSmsRequest();
        $request->setAccount($account);
        $request->setMobile($mobile);
        $request->setTplId($template->getTplId());
        $request->setTplValue($tplValue);
        $request->setUid($uid);

        $response = $this->apiClient->requestArray($request);

        $sendLog = new SendLog();
        $sendLog->setAccount($account);
        $sendLog->setTemplate($template);
        $sendLog->setMobile($mobile);
        $sendLog->setUid($uid);

        $text = $response['text'] ?? '';
        $sid = $response['sid'] ?? null;
        $count = $response['count'] ?? 0;
        $fee = $response['fee'] ?? '0.000';

        $sendLog->setContent($this->toString($text));
        $sendLog->setSid(is_string($sid) ? $sid : null);
        $sendLog->setCount($this->toInt($count));
        $sendLog->setFee($this->toString($fee, '0.000'));

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
        if ([] === $sendLogs) {
            return;
        }

        $groups = $this->groupSendLogsByAccount($sendLogs);
        $this->processSendLogGroups($groups);
    }

    /**
     * @param SendLog[] $sendLogs
     *
     * @return SendLog[][]
     */
    private function groupSendLogsByAccount(array $sendLogs): array
    {
        $groups = [];
        foreach ($sendLogs as $sendLog) {
            if (null !== $sendLog->getSid()) {
                $groups[$sendLog->getAccount()->getId()][] = $sendLog;
            }
        }

        return $groups;
    }

    /**
     * @param SendLog[][] $groups
     */
    private function processSendLogGroups(array $groups): void
    {
        foreach ($groups as $groupedSendLogs) {
            $this->processSingleGroup($groupedSendLogs);
        }
    }

    /**
     * @param SendLog[] $sendLogs
     */
    private function processSingleGroup(array $sendLogs): void
    {
        $account = $sendLogs[0]->getAccount();
        $sids = array_filter(array_map(fn (SendLog $sendLog) => $sendLog->getSid(), $sendLogs), fn (?string $sid) => null !== $sid);

        if ([] === $sids) {
            return;
        }

        try {
            $response = $this->fetchStatusResponse($account, $sids);
            $this->updateSendLogStatuses($sendLogs, $response);
            $this->entityManager->flush();
        } catch (\Throwable $e) {
            $this->logSyncError($e, $account, $sids);
        }
    }

    /**
     * @param string[] $sids
     *
     * @return array<string, mixed>
     */
    private function fetchStatusResponse(Account $account, array $sids): array
    {
        $request = new GetSendStatusRequest();
        $request->setAccount($account);
        $request->setSids($sids);

        return $this->apiClient->requestArray($request);
    }

    /**
     * @param SendLog[] $sendLogs
     * @param array<string, mixed> $response
     */
    private function updateSendLogStatuses(array $sendLogs, array $response): void
    {
        $sendLogMap = $this->createSendLogMap($sendLogs);

        foreach ($response as $status) {
            if (!is_array($status)) {
                continue;
            }

            $typedStatus = $this->ensureStringKeyedArray($status);
            $sid = $typedStatus['sid'] ?? null;
            if (is_string($sid) && isset($sendLogMap[$sid])) {
                $this->updateSendLogStatus($sendLogMap[$sid], $typedStatus);
            }
        }
    }

    /**
     * @param SendLog[] $sendLogs
     *
     * @return array<string, SendLog>
     */
    private function createSendLogMap(array $sendLogs): array
    {
        $sendLogMap = [];
        foreach ($sendLogs as $sendLog) {
            if (null !== $sendLog->getSid()) {
                $sendLogMap[$sendLog->getSid()] = $sendLog;
            }
        }

        return $sendLogMap;
    }

    /**
     * @param array<string, mixed> $status
     */
    private function updateSendLogStatus(SendLog $sendLog, array $status): void
    {
        $statusValue = $status['status'] ?? null;
        $statusMsg = $status['status_msg'] ?? null;
        $userReceiveTime = $status['user_receive_time'] ?? null;
        $errorMsg = $status['error_msg'] ?? null;

        $sendLog->setStatus(is_string($statusValue) ? $statusValue : null);
        $sendLog->setStatusMsg(is_string($statusMsg) ? $statusMsg : null);
        $sendLog->setReceiveTime(is_string($userReceiveTime) ? new \DateTimeImmutable($userReceiveTime) : null);
        $sendLog->setErrorMsg(is_string($errorMsg) ? $errorMsg : null);
    }

    /**
     * @param string[] $sids
     */
    private function logSyncError(\Throwable $e, Account $account, array $sids): void
    {
        $this->logger->error('同步发送状态失败: {message}', [
            'message' => $e->getMessage(),
            'exception' => $e,
            'account_id' => $account->getId(),
            'sids' => $sids,
        ]);
    }

    public function syncRecord(Account $account, \DateTimeInterface $startTime, \DateTimeInterface $endTime, ?string $mobile = null): void
    {
        $pageNum = 1;
        $pageSize = 100;
        $count = 0;

        while (true) {
            try {
                $response = $this->fetchRecordPage($account, $startTime, $endTime, $mobile, $pageNum, $pageSize);

                if ([] === $response) {
                    break;
                }

                $count = $this->processRecordPage($response, $account, $count, $pageSize);

                if (count($response) < $pageSize) {
                    break;
                }

                ++$pageNum;
            } catch (\Throwable $e) {
                $this->logRecordSyncError($e, $account, $startTime, $endTime, $mobile);
                throw $e;
            }
        }

        $this->flushRemainingRecords($count, $pageSize);
    }

    /**
     * @return array<string, mixed>
     */
    private function fetchRecordPage(Account $account, \DateTimeInterface $startTime, \DateTimeInterface $endTime, ?string $mobile, int $pageNum, int $pageSize): array
    {
        $request = new GetSendRecordRequest();
        $request->setAccount($account);
        $request->setStartTime($startTime);
        $request->setEndTime($endTime);
        $request->setMobile($mobile);
        $request->setPageNum((string) $pageNum);
        $request->setPageSize((string) $pageSize);

        return $this->apiClient->requestArray($request);
    }

    /**
     * @param array<string, mixed> $response
     */
    private function processRecordPage(array $response, Account $account, int $count, int $pageSize): int
    {
        foreach ($response as $record) {
            if (!is_array($record)) {
                continue;
            }
            $typedRecord = $this->ensureStringKeyedArray($record);
            $count = $this->processRecord($typedRecord, $account, $count, $pageSize);
        }

        return $count;
    }

    /**
     * @param array<string, mixed> $record
     */
    private function processRecord(array $record, Account $account, int $count, int $pageSize): int
    {
        $sid = $record['sid'] ?? null;
        if (!is_string($sid) || '' === $sid) {
            return $count;
        }

        $sendLog = $this->findOrCreateSendLog($sid, $account);
        $this->updateSendLogFromRecord($sendLog, $record);

        if (0 === ++$count % $pageSize) {
            $this->entityManager->flush();
            $this->entityManager->clear();
        }

        return $count;
    }

    private function findOrCreateSendLog(string $sid, Account $account): SendLog
    {
        $sendLog = $this->sendLogRepository->findOneBySid($sid);

        if (null === $sendLog) {
            $sendLog = new SendLog();
            $sendLog->setAccount($account);
            $sendLog->setSid($sid);
            $this->entityManager->persist($sendLog);
        }

        return $sendLog;
    }

    /**
     * @param array<string, mixed> $record
     */
    private function updateSendLogFromRecord(SendLog $sendLog, array $record): void
    {
        $mobile = $record['mobile'] ?? '';
        $text = $record['text'] ?? '';
        $count = $record['count'] ?? 0;
        $fee = $record['fee'] ?? '0.000';
        $uid = $record['uid'] ?? null;
        $status = $record['status'] ?? null;
        $statusMsg = $record['status_msg'] ?? null;
        $userReceiveTime = $record['user_receive_time'] ?? null;
        $errorMsg = $record['error_msg'] ?? null;

        $sendLog->setMobile($this->toString($mobile));
        $sendLog->setContent($this->toString($text));
        $sendLog->setCount($this->toInt($count));
        $sendLog->setFee($this->toString($fee, '0.000'));
        $sendLog->setUid(is_string($uid) ? $uid : null);
        $sendLog->setStatus(is_string($status) ? $status : null);
        $sendLog->setStatusMsg(is_string($statusMsg) ? $statusMsg : null);
        $sendLog->setReceiveTime(is_string($userReceiveTime) ? new \DateTimeImmutable($userReceiveTime) : null);
        $sendLog->setErrorMsg(is_string($errorMsg) ? $errorMsg : null);
    }

    private function logRecordSyncError(\Throwable $e, Account $account, \DateTimeInterface $startTime, \DateTimeInterface $endTime, ?string $mobile): void
    {
        $this->logger->error('同步发送记录失败: {message}', [
            'message' => $e->getMessage(),
            'exception' => $e,
            'account_id' => $account->getId(),
            'start_time' => $startTime->format('Y-m-d H:i:s'),
            'end_time' => $endTime->format('Y-m-d H:i:s'),
            'mobile' => $mobile,
        ]);
    }

    private function flushRemainingRecords(int $count, int $pageSize): void
    {
        if (0 !== $count % $pageSize) {
            $this->entityManager->flush();
        }
    }
}
