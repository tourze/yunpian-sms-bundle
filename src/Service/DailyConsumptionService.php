<?php

namespace YunpianSmsBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\DailyConsumption;
use YunpianSmsBundle\Repository\DailyConsumptionRepository;
use YunpianSmsBundle\Request\SMS\GetDailyConsumptionRequest;

class DailyConsumptionService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly DailyConsumptionRepository $dailyConsumptionRepository,
        private readonly SmsApiClient $apiClient,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function syncDailyConsumption(Account $account, \DateTimeInterface $date): void
    {
        $request = new GetDailyConsumptionRequest();
        $request->setAccount($account);
        $request->setDate($date);
        $response = $this->apiClient->requestArray($request);

        $consumption = $this->dailyConsumptionRepository->findOneByAccountAndDate($account, $date);
        if ($consumption === null) {
            $consumption = new DailyConsumption();
            $consumption->setAccount($account);
            $consumption->setDate($date);
        }

        $consumption->setTotalCount($response['totalCount'] ?? 0);
        $consumption->setTotalFee($response['totalFee'] ?? '0.000');
        $consumption->setTotalSuccessCount($response['totalSuccessCount'] ?? 0);
        $consumption->setTotalFailedCount($response['totalFailedCount'] ?? 0);
        $consumption->setTotalUnknownCount($response['totalUnknownCount'] ?? 0);

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
            
            if (empty($response['data'])) {
                return null;
            }
            
            $data = $response['data'];
            
            $consumption = $this->dailyConsumptionRepository->findOneBy([
                'account' => $account,
                'date' => $date
            ]);
            
            if ($consumption === null) {
                $consumption = new DailyConsumption();
                $consumption->setAccount($account);
                $consumption->setDate($date);
                $this->entityManager->persist($consumption);
            }
            
            $consumption->setTotalCount($data['total_sum'] ?? 0);
            $consumption->setTotalFee($data['total_fee'] ?? '0.000');
            if (isset($data['items'])) {
                $consumption->setItems($data['items']);
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
