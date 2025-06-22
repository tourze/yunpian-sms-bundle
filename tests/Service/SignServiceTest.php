<?php

namespace YunpianSmsBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\Sign;
use YunpianSmsBundle\Repository\SignRepository;
use YunpianSmsBundle\Service\SignService;
use YunpianSmsBundle\Service\SmsApiClient;

class SignServiceTest extends TestCase
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

    public function testSyncSigns_withValidAccount(): void
    {
        // 准备测试数据
        $account = $this->createAccount();
        
        $apiResponse = [
            [
                'sign' => '测试签名',
                'sign_id' => 1001,
                'apply_state' => 'SUCCESS',
                'enabled' => true,
                'website' => 'https://example.com'
            ]
        ];
        
        // 设置模拟对象预期行为
        $this->apiClient->expects($this->once())
            ->method('request')
            ->willReturn($apiResponse);
            
        $this->signRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['account' => $account, 'sign' => '测试签名'])
            ->willReturn(null);
            
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->willReturnCallback(function ($entity) use ($account) {
                $this->assertInstanceOf(Sign::class, $entity);
                $this->assertSame($account, $entity->getAccount());
                $this->assertEquals('测试签名', $entity->getSign());
                $entity->setSignId(1001); // 模拟设置ID
            });
            
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // 执行测试
        $result = $this->signService->syncSigns($account);
        
        // 断言结果
        $this->assertCount(1, $result);
        $this->assertInstanceOf(Sign::class, $result[0]);
        $this->assertEquals('测试签名', $result[0]->getSign());
        $this->assertEquals('SUCCESS', $result[0]->getApplyState());
        $this->assertTrue($result[0]->isValid());
        $this->assertEquals('https://example.com', $result[0]->getWebsite());
    }
    
    public function testSyncSigns_withExistingSign(): void
    {
        // 准备测试数据
        $account = $this->createAccount();
        
        $existingSign = new Sign();
        $existingSign->setAccount($account);
        $existingSign->setSign('旧签名');
        // 其他属性...
        
        $apiResponse = [
            [
                'sign' => '旧签名',
                'sign_id' => 1001,
                'apply_state' => 'SUCCESS',
                'enabled' => true,
                'website' => 'https://example.org'
            ]
        ];
        
        // 设置模拟对象预期行为
        $this->apiClient->expects($this->once())
            ->method('request')
            ->willReturn($apiResponse);
            
        $this->signRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['account' => $account, 'sign' => '旧签名'])
            ->willReturn($existingSign);
            
        $this->entityManager->expects($this->never())
            ->method('persist');
            
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // 执行测试
        $result = $this->signService->syncSigns($account);
        
        // 断言结果
        $this->assertCount(1, $result);
        $this->assertSame($existingSign, $result[0]);
        $this->assertEquals('旧签名', $existingSign->getSign());
        $this->assertEquals('SUCCESS', $existingSign->getApplyState());
        $this->assertTrue($existingSign->isValid());
    }
    
    public function testSyncSigns_withApiError(): void
    {
        // 准备测试数据
        $account = $this->createAccount();
        
        // 设置模拟对象预期行为
        $this->apiClient->expects($this->once())
            ->method('request')
            ->willThrowException(new \Exception('API错误'));
            
        $this->logger->expects($this->once())
            ->method('error')
            ->with('同步签名失败: {message}', $this->anything());
        
        // 执行测试
        $result = $this->signService->syncSigns($account);
        
        // 断言结果
        $this->assertEmpty($result);
    }

    public function testCreate_withValidParameters(): void
    {
        // 准备测试数据
        $account = $this->createAccount();
        $signContent = '测试签名';
        $remark = '测试备注';
        
        $apiResponse = [
            'sign_id' => 2001,
            'status' => 'SUCCESS'
        ];
        
        // 设置模拟对象预期行为
        $this->apiClient->expects($this->once())
            ->method('request')
            ->willReturn($apiResponse);
            
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function($entity) use ($account, $signContent, $remark) {
                // 测试开始时实体还没有ID，在service方法内才会设置ID
                // 但persist调用发生在ID设置后，此时可以验证
                $this->assertInstanceOf(Sign::class, $entity);
                $this->assertSame($account, $entity->getAccount());
                $this->assertEquals($signContent, $entity->getSign());
                $this->assertEquals($remark, $entity->getRemark());
                return true;
            }));
            
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // 执行测试
        $result = $this->signService->create($account, $signContent, $remark);
        
        // 断言结果
        $this->assertInstanceOf(Sign::class, $result);
        $this->assertSame($account, $result->getAccount());
        $this->assertEquals($signContent, $result->getSign());
        $this->assertEquals($remark, $result->getRemark());
    }
    
    public function testUpdate_withValidParameters(): void
    {
        // 准备测试数据
        $account = $this->createAccount();
        $sign = new Sign();
        $sign->setSign('测试签名');
        $sign->setAccount($account);
        
        $newSignContent = '新签名';
        
        $apiResponse = [
            'status' => 'SUCCESS'
        ];
        
        // 设置模拟对象预期行为
        $this->apiClient->expects($this->once())
            ->method('request')
            ->willReturn($apiResponse);
            
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // 执行测试
        $result = $this->signService->update($sign, $newSignContent);
        
        // 断言结果
        $this->assertSame($sign, $result);
        $this->assertEquals($newSignContent, $result->getSign());
    }
    
    public function testDelete_withValidSign(): void
    {
        // 准备测试数据
        $account = $this->createAccount();
        $sign = new Sign();
        $sign->setSign('测试签名');
        $sign->setAccount($account);
        
        $apiResponse = [
            'status' => 'SUCCESS'
        ];
        
        // 设置模拟对象预期行为
        $this->apiClient->expects($this->once())
            ->method('request')
            ->willReturn($apiResponse);
            
        $this->entityManager->expects($this->once())
            ->method('remove')
            ->with($sign);
            
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // 执行测试
        $result = $this->signService->delete($sign);
        
        // 断言结果
        $this->assertTrue($result);
    }
    
    public function testDelete_withApiError(): void
    {
        // 准备测试数据
        $account = $this->createAccount();
        $sign = new Sign();
        $sign->setSign('测试签名');
        $sign->setAccount($account);
        
        // 设置模拟对象预期行为
        $this->apiClient->expects($this->once())
            ->method('request')
            ->willThrowException(new \Exception('API错误'));
            
        $this->logger->expects($this->once())
            ->method('error')
            ->with('删除签名失败: {message}', $this->anything());
            
        $this->entityManager->expects($this->never())
            ->method('remove');
            
        $this->entityManager->expects($this->never())
            ->method('flush');
        
        // 执行测试
        $result = $this->signService->delete($sign);
        
        // 断言结果
        $this->assertFalse($result);
    }

    // 辅助方法：创建测试用的Account实例
    private function createAccount(): Account
    {
        return \YunpianSmsBundle\Tests\Mock\MockHelper::createAccount();
    }
} 