<?php

namespace YunpianSmsBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
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
    ) {
    }

    public function syncDailyConsumption(Account $account, \DateTimeInterface $date): void
    {
        $request = new GetDailyConsumptionRequest();
        $request->setAccount($account);
        $request->setDate($date);
        $response = $this->apiClient->request($request);

        $consumption = $this->dailyConsumptionRepository->findOneByAccountAndDate($account, $date);
        if (!$consumption) {
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
}
