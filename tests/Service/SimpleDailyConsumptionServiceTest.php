<?php

namespace YunpianSmsBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use YunpianSmsBundle\Entity\DailyConsumption;
use YunpianSmsBundle\Repository\DailyConsumptionRepository;
use YunpianSmsBundle\Service\DailyConsumptionService;
use YunpianSmsBundle\Service\SmsApiClient;
use YunpianSmsBundle\Tests\Mock\MockHelper;

class SimpleDailyConsumptionServiceTest extends TestCase
{
    public function testCreate(): void
    {
        // 创建Mock对象
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $repository = $this->createMock(DailyConsumptionRepository::class);
        $apiClient = $this->createMock(SmsApiClient::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        // 创建测试数据
        $account = MockHelper::createAccount();
        $date = new \DateTime('2023-05-01');
        $totalCount = 100;
        $totalFee = '50.00';
        $items = [
            ['count' => 50, 'fee' => '25.00', 'name' => '国内短信'],
            ['count' => 50, 'fee' => '25.00', 'name' => '国际短信']
        ];
        
        // 创建测试对象
        $service = new DailyConsumptionService(
            $entityManager,
            $repository,
            $apiClient,
            $logger
        );
        
        // 设置期望
        $entityManager->expects($this->once())
            ->method('persist')
            ->willReturnCallback(function ($entity) use ($account, $date, $totalCount, $totalFee, $items) {
                $this->assertInstanceOf(DailyConsumption::class, $entity);
                $this->assertSame($account, $entity->getAccount());
                $this->assertEquals($date->format('Y-m-d'), $entity->getDate()->format('Y-m-d'));
                $this->assertEquals($totalCount, $entity->getTotalCount());
                $this->assertEquals($totalFee, $entity->getTotalFee());
                $this->assertEquals($items, $entity->getItems());
            });
            
        $entityManager->expects($this->once())
            ->method('flush');
        
        // 执行测试
        $result = $service->create($account, $date, $totalCount, $totalFee, $items);
        
        // 断言
        $this->assertInstanceOf(DailyConsumption::class, $result);
        $this->assertSame($account, $result->getAccount());
        $this->assertEquals($date->format('Y-m-d'), $result->getDate()->format('Y-m-d'));
        $this->assertEquals($totalCount, $result->getTotalCount());
        $this->assertEquals($totalFee, $result->getTotalFee());
        $this->assertEquals($items, $result->getItems());
    }
    
    public function testSyncConsumptionMock(): void
    {
        // 创建Mock对象
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $repository = $this->createMock(DailyConsumptionRepository::class);
        $apiClient = $this->createMock(SmsApiClient::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        // 创建测试数据
        $account = MockHelper::createAccount();
        $date = new \DateTime('2023-05-01');
        
        // API响应
        $apiResponse = [
            'code' => 0,
            'msg' => 'success',
            'data' => [
                'total_sum' => 100,
                'total_fee' => '50.00',
                'items' => [
                    ['count' => 50, 'fee' => '25.00', 'name' => '国内短信'],
                    ['count' => 50, 'fee' => '25.00', 'name' => '国际短信']
                ]
            ]
        ];
        
        // 设置Mock行为
        $apiClient->expects($this->once())
            ->method('requestArray')
            ->willReturn($apiResponse);
            
        $repository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);
            
        $entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function($entity) use ($account, $date) {
                $this->assertInstanceOf(DailyConsumption::class, $entity);
                $this->assertSame($account, $entity->getAccount());
                $this->assertEquals($date->format('Y-m-d'), $entity->getDate()->format('Y-m-d'));
                return true;
            }));
            
        $entityManager->expects($this->once())
            ->method('flush');
            
        // 创建服务对象
        $service = new DailyConsumptionService(
            $entityManager,
            $repository,
            $apiClient,
            $logger
        );
            
        // 执行测试
        $result = $service->syncConsumption($account, $date);
        
        // 断言
        $this->assertInstanceOf(DailyConsumption::class, $result);
        $this->assertEquals(100, $result->getTotalCount());
        $this->assertEquals('50.00', $result->getTotalFee());
    }
} 