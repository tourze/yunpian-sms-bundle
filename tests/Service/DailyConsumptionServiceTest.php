<?php

namespace YunpianSmsBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\DailyConsumption;
use YunpianSmsBundle\Repository\DailyConsumptionRepository;
use YunpianSmsBundle\Service\DailyConsumptionService;
use YunpianSmsBundle\Service\SmsApiClient;

class DailyConsumptionServiceTest extends TestCase
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

    public function testSyncConsumption_withValidParameters(): void
    {
        // 准备测试数据
        $account = $this->createAccount();
        $date = new \DateTime('2023-05-01');
        
        $apiResponse = [
            'code' => 0,
            'msg' => 'success',
            'data' => [
                'total_sum' => 100,
                'total_fee' => '50.00',
                'items' => [
                    [
                        'count' => 50,
                        'fee' => '25.00',
                        'name' => '国内短信'
                    ],
                    [
                        'count' => 50,
                        'fee' => '25.00',
                        'name' => '国际短信'
                    ]
                ]
            ]
        ];

        // 设置模拟对象预期行为
        $this->apiClient->expects($this->once())
            ->method('request')
            ->willReturn($apiResponse);
            
        $this->dailyConsumptionRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['account' => $account, 'date' => $date])
            ->willReturn(null);
            
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function($entity) use ($account, $date) {
                $this->assertInstanceOf(DailyConsumption::class, $entity);
                $this->assertSame($account, $entity->getAccount());
                $this->assertEquals($date->format('Y-m-d'), $entity->getDate()->format('Y-m-d'));
                return true;
            }));
            
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // 执行测试
        $result = $this->dailyConsumptionService->syncConsumption($account, $date);
        
        // 断言结果
        $this->assertInstanceOf(DailyConsumption::class, $result);
        $this->assertSame($account, $result->getAccount());
        $this->assertEquals($date->format('Y-m-d'), $result->getDate()->format('Y-m-d'));
        $this->assertEquals(100, $result->getTotalCount());
        $this->assertEquals('50.00', $result->getTotalFee());
        $this->assertIsArray($result->getItems());
    }
    
    public function testSyncConsumption_withExistingRecord(): void
    {
        // 准备测试数据
        $account = $this->createAccount();
        $date = new \DateTime('2023-05-01');
        
        $existingRecord = new DailyConsumption();
        $existingRecord->setAccount($account);
        $existingRecord->setDate($date);
        $existingRecord->setTotalCount(50);
        $existingRecord->setTotalFee('25.00');
        $existingRecord->setItems([
            ['count' => 50, 'fee' => '25.00', 'name' => '国内短信']
        ]);
        
        $apiResponse = [
            'code' => 0,
            'msg' => 'success',
            'data' => [
                'total_sum' => 100,
                'total_fee' => '50.00',
                'items' => [
                    [
                        'count' => 100,
                        'fee' => '50.00',
                        'name' => '国内短信'
                    ]
                ]
            ]
        ];
        
        // 设置模拟对象预期行为
        $this->apiClient->expects($this->once())
            ->method('request')
            ->willReturn($apiResponse);
            
        $this->dailyConsumptionRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['account' => $account, 'date' => $date])
            ->willReturn($existingRecord);
            
        $this->entityManager->expects($this->never())
            ->method('persist');
            
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // 执行测试
        $result = $this->dailyConsumptionService->syncConsumption($account, $date);
        
        // 断言结果
        $this->assertSame($existingRecord, $result);
        $this->assertEquals(100, $result->getTotalCount());
        $this->assertEquals('50.00', $result->getTotalFee());
        $this->assertEquals([
            ['count' => 100, 'fee' => '50.00', 'name' => '国内短信']
        ], $result->getItems());
    }
    
    public function testSyncConsumption_withEmptyResponse(): void
    {
        // 准备测试数据
        $account = $this->createAccount();
        $date = new \DateTime('2023-05-01');
        
        $apiResponse = [
            'code' => 0,
            'msg' => 'success',
            'data' => []
        ];
        
        // 设置模拟对象预期行为
        $this->apiClient->expects($this->once())
            ->method('request')
            ->willReturn($apiResponse);
            
        $this->dailyConsumptionRepository->expects($this->never())
            ->method('findOneBy');
            
        $this->entityManager->expects($this->never())
            ->method('persist');
            
        $this->entityManager->expects($this->never())
            ->method('flush');
        
        // 执行测试
        $result = $this->dailyConsumptionService->syncConsumption($account, $date);
        
        // 断言结果
        $this->assertNull($result);
    }
    
    public function testSyncConsumption_withApiError(): void
    {
        // 准备测试数据
        $account = $this->createAccount();
        $date = new \DateTime('2023-05-01');
        
        // 设置模拟对象预期行为
        $this->apiClient->expects($this->once())
            ->method('request')
            ->willThrowException(new \Exception('API错误'));
            
        $this->logger->expects($this->once())
            ->method('error')
            ->with('同步每日消费统计失败: {message}', $this->anything());
            
        $this->dailyConsumptionRepository->expects($this->never())
            ->method('findOneBy');
            
        $this->entityManager->expects($this->never())
            ->method('persist');
            
        $this->entityManager->expects($this->never())
            ->method('flush');
        
        // 执行测试
        $result = $this->dailyConsumptionService->syncConsumption($account, $date);
        
        // 断言结果
        $this->assertNull($result);
    }

    public function testCreate_withValidParameters(): void
    {
        // 准备测试数据
        $account = $this->createAccount();
        $date = new \DateTime('2023-05-01');
        $totalCount = 100;
        $totalFee = '50.00';
        $items = [
            ['count' => 50, 'fee' => '25.00', 'name' => '国内短信'],
            ['count' => 50, 'fee' => '25.00', 'name' => '国际短信']
        ];
        
        // 设置模拟对象预期行为
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->willReturnCallback(function ($entity) use ($account, $date, $totalCount, $totalFee, $items) {
                $this->assertInstanceOf(DailyConsumption::class, $entity);
                $this->assertSame($account, $entity->getAccount());
                $this->assertEquals($date->format('Y-m-d'), $entity->getDate()->format('Y-m-d'));
                $this->assertEquals($totalCount, $entity->getTotalCount());
                $this->assertEquals($totalFee, $entity->getTotalFee());
                $this->assertEquals($items, $entity->getItems());
            });
            
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // 执行测试
        $result = $this->dailyConsumptionService->create($account, $date, $totalCount, $totalFee, $items);
        
        // 断言结果
        $this->assertInstanceOf(DailyConsumption::class, $result);
        $this->assertSame($account, $result->getAccount());
        $this->assertEquals($date->format('Y-m-d'), $result->getDate()->format('Y-m-d'));
        $this->assertEquals($totalCount, $result->getTotalCount());
        $this->assertEquals($totalFee, $result->getTotalFee());
        $this->assertEquals($items, $result->getItems());
    }

    // 辅助方法：创建测试用的Account实例
    private function createAccount(): Account
    {
        return \YunpianSmsBundle\Tests\Mock\MockHelper::createAccount();
    }
} 