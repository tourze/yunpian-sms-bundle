<?php

namespace YunpianSmsBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\SendLog;
use YunpianSmsBundle\Entity\Template;
use YunpianSmsBundle\Enum\SendStatusEnum;
use YunpianSmsBundle\Repository\SendLogRepository;
use YunpianSmsBundle\Request\GetSendRecordRequest;
use YunpianSmsBundle\Request\GetSendStatusRequest;
use YunpianSmsBundle\Request\SendSmsRequest;
use YunpianSmsBundle\Request\SendTplSmsRequest;
use YunpianSmsBundle\Service\SendLogService;
use YunpianSmsBundle\Service\SmsApiClient;

class SendLogServiceTest extends TestCase
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

    public function testSend_withValidParameters(): void
    {
        // 准备测试数据
        $account = $this->createAccount();
        $mobile = '13800138000';
        $content = '您的验证码是1234';
        $uid = 'test-uid';
        
        $apiResponse = [
            'code' => 0,
            'msg' => 'success',
            'sid' => 10001,
            'count' => 1,
            'fee' => '0.050'
        ];
        
        // 设置模拟对象预期行为
        $this->apiClient->expects($this->once())
            ->method('requestArray')
            ->willReturnCallback(function ($request) use ($apiResponse) {
                $this->assertInstanceOf(SendSmsRequest::class, $request);
                return $apiResponse;
            });
        
        // 执行测试
        $result = $this->sendLogService->send($account, $mobile, $content, $uid);
        
        // 断言结果
        $this->assertInstanceOf(SendLog::class, $result);
        $this->assertSame($account, $result->getAccount());
        $this->assertSame($mobile, $result->getMobile());
        $this->assertSame($content, $result->getContent());
        $this->assertSame($uid, $result->getUid());
        $this->assertEquals(10001, $result->getSid());
        $this->assertSame(1, $result->getCount());
        $this->assertSame('0.050', $result->getFee());
    }
    
    public function testSendTpl_withValidParameters(): void
    {
        // 准备测试数据
        $account = $this->createAccount();
        $template = $this->createTemplate();
        $mobile = '13800138000';
        $tplValue = ['code' => '1234'];
        $uid = 'test-uid';
        
        $apiResponse = [
            'code' => 0,
            'msg' => 'success',
            'sid' => 10002,
            'count' => 1,
            'fee' => '0.050',
            'text' => '您的验证码是1234'
        ];
        
        // 设置模拟对象预期行为
        $this->apiClient->expects($this->once())
            ->method('requestArray')
            ->willReturnCallback(function ($request) use ($apiResponse, $template) {
                $this->assertInstanceOf(SendTplSmsRequest::class, $request);
                $this->assertEquals($template->getTplId(), $request->getTplId());
                return $apiResponse;
            });
        
        // 执行测试
        $result = $this->sendLogService->sendTpl($account, $template, $mobile, $tplValue, $uid);
        
        // 断言结果
        $this->assertInstanceOf(SendLog::class, $result);
        $this->assertSame($account, $result->getAccount());
        $this->assertSame($template, $result->getTemplate());
        $this->assertSame($mobile, $result->getMobile());
        $this->assertSame('您的验证码是1234', $result->getContent());
        $this->assertSame($uid, $result->getUid());
        $this->assertEquals(10002, $result->getSid());
        $this->assertEquals(1, $result->getCount());
        $this->assertEquals('0.050', $result->getFee());
    }
    
    public function testCreate_withValidParameters(): void
    {
        // 准备测试数据
        $account = $this->createAccount();
        $template = $this->createTemplate();
        $mobile = '13800138000';
        $content = '您的验证码是1234';
        $uid = 'test-uid';
        
        // 设置模拟对象预期行为
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->willReturnCallback(function ($entity) use ($account, $template, $mobile, $content, $uid) {
                $this->assertInstanceOf(SendLog::class, $entity);
                $this->assertSame($account, $entity->getAccount());
                $this->assertSame($template, $entity->getTemplate());
                $this->assertSame($mobile, $entity->getMobile());
                $this->assertSame($content, $entity->getContent());
                $this->assertSame($uid, $entity->getUid());
            });
            
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // 执行测试
        $result = $this->sendLogService->create($account, $mobile, $content, $template, $uid);
        
        // 断言结果
        $this->assertInstanceOf(SendLog::class, $result);
        $this->assertSame($account, $result->getAccount());
        $this->assertSame($template, $result->getTemplate());
        $this->assertSame($mobile, $result->getMobile());
        $this->assertSame($content, $result->getContent());
        $this->assertSame($uid, $result->getUid());
    }
    
    public function testSyncStatus_withPendingLogs(): void
    {
        // 准备测试数据
        $account = $this->createAccount();
        $sendLog1 = $this->createSendLog($account, 1001);
        $sendLog2 = $this->createSendLog($account, 1002);
        $pendingLogs = [$sendLog1, $sendLog2];
        
        $statusResponse = [
            [
                'sid' => 1001,
                'status' => 'success', // 使用字符串状态
                'status_msg' => 'SUCCESS',
                'user_receive_time' => '2023-05-01 12:00:00',
                'error_msg' => null
            ],
            [
                'sid' => 1002,
                'status' => 'fail', // 使用字符串状态
                'status_msg' => 'FAILED',
                'user_receive_time' => null,
                'error_msg' => '手机号无效'
            ]
        ];
        
        // 设置模拟对象预期行为
        $this->sendLogRepository->expects($this->once())
            ->method('findPendingStatus')
            ->willReturn($pendingLogs);
            
        $this->apiClient->expects($this->once())
            ->method('requestArray')
            ->willReturnCallback(function ($request) use ($statusResponse) {
                $this->assertInstanceOf(GetSendStatusRequest::class, $request);
                $this->assertEquals([1001, 1002], $request->getSids());
                return $statusResponse;
            });
            
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // 执行测试
        $this->sendLogService->syncStatus();
        
        // 断言结果 - 使用枚举进行比较
        $this->assertEquals(SendStatusEnum::SUCCESS, $sendLog1->getStatus());
        $this->assertEquals('SUCCESS', $sendLog1->getStatusMsg());
        $this->assertEquals('2023-05-01 12:00:00', $sendLog1->getReceiveTime()->format('Y-m-d H:i:s'));
        
        $this->assertEquals(SendStatusEnum::FAILED, $sendLog2->getStatus());
        $this->assertEquals('FAILED', $sendLog2->getStatusMsg());
        $this->assertEquals('手机号无效', $sendLog2->getErrorMsg());
    }

    public function testSyncStatus_withEmptyPendingLogs(): void
    {
        // 设置模拟对象预期行为
        $this->sendLogRepository->expects($this->once())
            ->method('findPendingStatus')
            ->willReturn([]);
            
        $this->apiClient->expects($this->never())
            ->method('requestArray');
            
        $this->entityManager->expects($this->never())
            ->method('flush');
        
        // 执行测试
        $this->sendLogService->syncStatus();
    }
    
    public function testSyncStatus_withApiError(): void
    {
        // 准备测试数据
        $account = $this->createAccount();
        $sendLog = $this->createSendLog($account, 1001);
        $pendingLogs = [$sendLog];
        
        // 设置模拟对象预期行为
        $this->sendLogRepository->expects($this->once())
            ->method('findPendingStatus')
            ->willReturn($pendingLogs);
            
        $this->apiClient->expects($this->once())
            ->method('requestArray')
            ->willThrowException(new \Exception('API错误'));
            
        $this->logger->expects($this->once())
            ->method('error')
            ->with('同步发送状态失败: {message}', $this->anything());
        
        // 执行测试
        $this->sendLogService->syncStatus();
    }

    public function testSyncRecord_withValidParameters(): void
    {
        // 准备测试数据
        $account = $this->createAccount();
        $startTime = new \DateTime('2023-05-01');
        $endTime = new \DateTime('2023-05-02');
        $mobile = '13800138000';
        
        $recordResponse = [
            [
                'sid' => 2001,
                'mobile' => '13800138000',
                'text' => '您的验证码是1234',
                'count' => 1,
                'fee' => '0.050',
                'uid' => 'test-uid-1',
                'status' => 1,
                'status_msg' => 'SUCCESS',
                'user_receive_time' => '2023-05-01 10:00:00',
                'error_msg' => null
            ],
            [
                'sid' => 2002,
                'mobile' => '13900139000',
                'text' => '您的验证码是5678',
                'count' => 1,
                'fee' => '0.050',
                'uid' => 'test-uid-2',
                'status' => 2,
                'status_msg' => 'FAILED',
                'user_receive_time' => null,
                'error_msg' => '手机号无效'
            ]
        ];
        
        // 设置模拟对象预期行为
        $this->apiClient->expects($this->once())
            ->method('requestArray')
            ->willReturnCallback(function ($request) use ($recordResponse, $startTime, $endTime, $mobile) {
                $this->assertInstanceOf(GetSendRecordRequest::class, $request);
                $this->assertEquals($startTime, $request->getStartTime());
                $this->assertEquals($endTime, $request->getEndTime());
                $this->assertEquals($mobile, $request->getMobile());
                return $recordResponse;
            });
            
        $this->sendLogRepository->expects($this->exactly(2))
            ->method('findOneBySid')
            ->willReturn(null);
            
        $this->entityManager->expects($this->exactly(2))
            ->method('persist');
            
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // 执行测试
        $this->sendLogService->syncRecord($account, $startTime, $endTime, $mobile);
    }
    
    public function testSyncRecord_withApiError(): void
    {
        // 准备测试数据
        $account = $this->createAccount();
        $startTime = new \DateTime('2023-05-01');
        $endTime = new \DateTime('2023-05-02');
        
        // 设置模拟对象预期行为
        $this->apiClient->expects($this->once())
            ->method('requestArray')
            ->willThrowException(new \Exception('API错误'));
            
        $this->logger->expects($this->once())
            ->method('error')
            ->with('同步发送记录失败: {message}', $this->anything());
        
        // 预期会抛出异常
        $this->expectException(\Exception::class);
        
        // 执行测试
        $this->sendLogService->syncRecord($account, $startTime, $endTime);
    }

    // 辅助方法：创建测试用的Account实例
    private function createAccount(): Account
    {
        return \YunpianSmsBundle\Tests\Mock\MockHelper::createAccount();
    }
    
    // 辅助方法：创建测试用的Template实例
    private function createTemplate(): Template
    {
        return \YunpianSmsBundle\Tests\Mock\MockHelper::createTemplate($this->createAccount());
    }
    
    // 辅助方法：创建测试用的SendLog实例
    private function createSendLog(Account $account, int $sid): SendLog
    {
        $sendLog = new SendLog();
        $sendLog->setAccount($account);
        $sendLog->setSid((string)$sid);
        $sendLog->setMobile('13800138000');
        $sendLog->setContent('测试内容');
        return $sendLog;
    }
} 