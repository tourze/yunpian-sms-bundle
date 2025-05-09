<?php

namespace YunpianSmsBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use YunpianSmsBundle\Repository\TemplateRepository;
use YunpianSmsBundle\Service\SmsApiClient;
use YunpianSmsBundle\Service\TemplateService;

class TemplateServiceBasicTest extends TestCase
{
    private TemplateService $templateService;
    private MockObject $entityManager;
    private MockObject $templateRepository;
    private MockObject $apiClient;
    private MockObject $logger;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->templateRepository = $this->createMock(TemplateRepository::class);
        $this->apiClient = $this->createMock(SmsApiClient::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->templateService = new TemplateService(
            $this->entityManager,
            $this->templateRepository,
            $this->apiClient,
            $this->logger
        );
    }
    
    public function testServiceCanBeCreated(): void
    {
        $this->assertInstanceOf(TemplateService::class, $this->templateService);
    }
} 