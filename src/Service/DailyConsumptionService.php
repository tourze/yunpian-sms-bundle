<?php

namespace YunpianSmsBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\DailyConsumption;
use YunpianSmsBundle\Repository\DailyConsumptionRepository;
use YunpianSmsBundle\Request\SMS\GetDailyConsumptionRequest;

#[WithMonologChannel(channel: 'yunpian_sms')]
class DailyConsumptionService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly DailyConsumptionRepository $dailyConsumptionRepository,
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

    public function syncDailyConsumption(Account $account, \DateTimeInterface $date): void
    {
        $request = new GetDailyConsumptionRequest();
        $request->setAccount($account);
        $request->setDate($date);
        $response = $this->apiClient->requestArray($request);

        $consumption = $this->dailyConsumptionRepository->findOneByAccountAndDate($account, $date);
        if (null === $consumption) {
            $consumption = new DailyConsumption();
            $consumption->setAccount($account);
            $consumption->setDate($date);
        }

        $totalCount = $response['totalCount'] ?? 0;
        $totalFee = $response['totalFee'] ?? '0.000';
        $totalSuccessCount = $response['totalSuccessCount'] ?? 0;
        $totalFailedCount = $response['totalFailedCount'] ?? 0;
        $totalUnknownCount = $response['totalUnknownCount'] ?? 0;

        $consumption->setTotalCount($this->toInt($totalCount));
        $consumption->setTotalFee($this->toString($totalFee, '0.000'));
        $consumption->setTotalSuccessCount($this->toInt($totalSuccessCount));
        $consumption->setTotalFailedCount($this->toInt($totalFailedCount));
        $consumption->setTotalUnknownCount($this->toInt($totalUnknownCount));

        $this->entityManager->persist($consumption);
        $this->entityManager->flush();
    }

    /**
     * 同步每日消费统计
     */
    public function syncConsumption(Account $account, \DateTimeInterface $date): ?DailyConsumption
    {
        try {
            $request = new GetDailyConsumptionRequest();
            $request->setAccount($account);
            $request->setDate($date);
            $response = $this->apiClient->requestArray($request);

            $responseData = $response['data'] ?? null;
            if (null === $responseData || !is_array($responseData)) {
                return null;
            }

            $consumption = $this->dailyConsumptionRepository->findOneBy([
                'account' => $account,
                'date' => $date,
            ]);

            if (null === $consumption) {
                $consumption = new DailyConsumption();
                $consumption->setAccount($account);
                $consumption->setDate($date);
                $this->entityManager->persist($consumption);
            }

            $totalSum = $responseData['total_sum'] ?? 0;
            $totalFee = $responseData['total_fee'] ?? '0.000';
            $items = $responseData['items'] ?? null;

            $consumption->setTotalCount($this->toInt($totalSum));
            $consumption->setTotalFee($this->toString($totalFee, '0.000'));

            if (is_array($items)) {
                $consumption->setItems($items);
            }

            $this->entityManager->flush();

            return $consumption;
        } catch (\Throwable $e) {
            $this->logger->error('同步每日消费统计失败: {message}', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return null;
        }
    }

    /**
     * 创建每日消费统计记录
     *
     * @param array<mixed> $items
     */
    public function create(Account $account, \DateTimeInterface $date, int $totalCount, string $totalFee, array $items = []): DailyConsumption
    {
        $consumption = new DailyConsumption();
        $consumption->setAccount($account);
        $consumption->setDate($date);
        $consumption->setTotalCount($totalCount);
        $consumption->setTotalFee($totalFee);
        $consumption->setItems($items);

        $this->entityManager->persist($consumption);
        $this->entityManager->flush();

        return $consumption;
    }
}
