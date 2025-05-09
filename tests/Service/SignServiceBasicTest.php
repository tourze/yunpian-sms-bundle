<?php

namespace YunpianSmsBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use YunpianSmsBundle\Repository\SignRepository;
use YunpianSmsBundle\Service\SignService;
use YunpianSmsBundle\Service\SmsApiClient;

class SignServiceBasicTest extends TestCase
{
    private SignService $signService;
    private MockObject $entityManager;
    private MockObject $signRepository;
    private MockObject $apiClient;
    private MockObject $logger;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->signRepository = $this->createMock(SignRepository::class);
        $this->apiClient = $this->createMock(SmsApiClient::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->signService = new SignService(
            $this->entityManager,
            $this->signRepository,
            $this->apiClient,
            $this->logger
        );
    }
    
    public function testServiceCanBeCreated(): void
    {
        $this->assertInstanceOf(SignService::class, $this->signService);
    }
} 