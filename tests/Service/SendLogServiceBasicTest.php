<?php

namespace YunpianSmsBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use YunpianSmsBundle\Repository\SendLogRepository;
use YunpianSmsBundle\Service\SendLogService;
use YunpianSmsBundle\Service\SmsApiClient;

class SendLogServiceBasicTest extends TestCase
{
    private SendLogService $sendLogService;
    private MockObject $entityManager;
    private MockObject $sendLogRepository;
    private MockObject $apiClient;
    private MockObject $logger;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->sendLogRepository = $this->createMock(SendLogRepository::class);
        $this->apiClient = $this->createMock(SmsApiClient::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->sendLogService = new SendLogService(
            $this->entityManager,
            $this->sendLogRepository,
            $this->apiClient,
            $this->logger
        );
    }
    
    public function testServiceCanBeCreated(): void
    {
        $this->assertInstanceOf(SendLogService::class, $this->sendLogService);
    }
} 