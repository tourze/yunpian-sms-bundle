<?php

namespace YunpianSmsBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use YunpianSmsBundle\Repository\DailyConsumptionRepository;
use YunpianSmsBundle\Service\DailyConsumptionService;
use YunpianSmsBundle\Service\SmsApiClient;

class DailyConsumptionServiceBasicTest extends TestCase
{
    private DailyConsumptionService $dailyConsumptionService;
    private MockObject $entityManager;
    private MockObject $dailyConsumptionRepository;
    private MockObject $apiClient;
    private MockObject $logger;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->dailyConsumptionRepository = $this->createMock(DailyConsumptionRepository::class);
        $this->apiClient = $this->createMock(SmsApiClient::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->dailyConsumptionService = new DailyConsumptionService(
            $this->entityManager,
            $this->dailyConsumptionRepository,
            $this->apiClient,
            $this->logger
        );
    }
    
    public function testServiceCanBeCreated(): void
    {
        $this->assertInstanceOf(DailyConsumptionService::class, $this->dailyConsumptionService);
    }
} 