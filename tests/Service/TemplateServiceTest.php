<?php

namespace YunpianSmsBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\Template;
use YunpianSmsBundle\Repository\TemplateRepository;
use YunpianSmsBundle\Request\Template\AddTemplateRequest;
use YunpianSmsBundle\Request\Template\DeleteTemplateRequest;
use YunpianSmsBundle\Request\Template\GetTemplateRequest;
use YunpianSmsBundle\Request\Template\UpdateTemplateRequest;
use YunpianSmsBundle\Service\SmsApiClient;
use YunpianSmsBundle\Service\TemplateService;

class TemplateServiceTest extends TestCase
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

    public function testSyncTemplates_withValidAccount(): void
    {
        // 准备测试数据
        $account = $this->createAccount();
        
        $apiResponse = [
            [
                'tpl_id' => 'tpl001',
                'tpl_content' => '您的验证码是#code#',
                'check_status' => 'SUCCESS',
                'reason' => '',
                'create_time' => '2023-01-01 12:00:00'
            ],
            [
                'tpl_id' => 'tpl002',
                'tpl_content' => '尊敬的#name#，您的订单#order#已发货',
                'check_status' => 'CHECKING',
                'reason' => null,
                'create_time' => '2023-01-02 14:30:00'
            ]
        ];
        
        // 设置模拟对象预期行为
        $this->apiClient->expects($this->once())
            ->method('requestArray')
            ->willReturnCallback(function ($request) use ($apiResponse) {
                $this->assertInstanceOf(GetTemplateRequest::class, $request);
                return $apiResponse;
            });
            
        $this->templateRepository->expects($this->exactly(2))
            ->method('findOneBy')
            ->willReturn(null);
            
        $this->entityManager->expects($this->exactly(2))
            ->method('persist')
            ->willReturnCallback(function ($entity) {
                $this->assertInstanceOf(Template::class, $entity);
            });
            
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // 执行测试
        $result = $this->templateService->syncTemplates($account);
        
        // 断言结果
        $this->assertCount(2, $result);
        $this->assertInstanceOf(Template::class, $result[0]);
        $this->assertInstanceOf(Template::class, $result[1]);
        $this->assertEquals('tpl001', $result[0]->getTplId());
        $this->assertEquals('tpl002', $result[1]->getTplId());
    }
    
    public function testSyncTemplates_withExistingTemplate(): void
    {
        // 准备测试数据
        $account = $this->createAccount();
        $existingTemplate = new Template();
        $existingTemplate->setTplId('tpl001');
        $existingTemplate->setContent('旧内容');
        $existingTemplate->setAccount($account);
        
        $apiResponse = [
            [
                'tpl_id' => 'tpl001',
                'tpl_content' => '新内容',
                'check_status' => 'SUCCESS',
                'reason' => '',
                'create_time' => '2023-01-01 12:00:00'
            ]
        ];
        
        // 设置模拟对象预期行为
        $this->apiClient->expects($this->once())
            ->method('requestArray')
            ->willReturn($apiResponse);
            
        $this->templateRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['account' => $account, 'tplId' => 'tpl001'])
            ->willReturn($existingTemplate);
            
        $this->entityManager->expects($this->never())
            ->method('persist');
            
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // 执行测试
        $result = $this->templateService->syncTemplates($account);
        
        // 断言结果
        $this->assertCount(1, $result);
        $this->assertSame($existingTemplate, $result[0]);
        $this->assertEquals('新内容', $existingTemplate->getContent());
    }
    
    public function testSyncTemplates_withApiError(): void
    {
        // 准备测试数据
        $account = $this->createAccount();
        
        // 设置模拟对象预期行为
        $this->apiClient->expects($this->once())
            ->method('requestArray')
            ->willThrowException(new \Exception('API错误'));
            
        $this->logger->expects($this->once())
            ->method('error')
            ->with('同步模板失败: {message}', $this->anything());
        
        // 执行测试
        $result = $this->templateService->syncTemplates($account);
        
        // 断言结果
        $this->assertEmpty($result);
    }

    public function testCreate_withValidParameters(): void
    {
        // 准备测试数据
        $account = $this->createAccount();
        $tplContent = '您的验证码是#code#';
        $notify = true;
        
        $apiResponse = [
            'tpl_id' => 'new_tpl_001',
            'status' => 'SUCCESS'
        ];
        
        // 设置模拟对象预期行为
        $this->apiClient->expects($this->once())
            ->method('requestArray')
            ->willReturnCallback(function ($request) use ($apiResponse, $tplContent) {
                $this->assertInstanceOf(AddTemplateRequest::class, $request);
                $this->assertEquals($tplContent, $request->getContent());
                return $apiResponse;
            });
            
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->willReturnCallback(function ($entity) use ($account, $tplContent) {
                $this->assertInstanceOf(Template::class, $entity);
                $this->assertSame($account, $entity->getAccount());
                $this->assertEquals('new_tpl_001', $entity->getTplId());
                $this->assertEquals($tplContent, $entity->getContent());
            });
            
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // 执行测试
        $result = $this->templateService->create($account, $tplContent, $notify);
        
        // 断言结果
        $this->assertInstanceOf(Template::class, $result);
        $this->assertSame($account, $result->getAccount());
        $this->assertEquals('new_tpl_001', $result->getTplId());
        $this->assertEquals($tplContent, $result->getContent());
    }
    
    public function testUpdate_withValidParameters(): void
    {
        // 准备测试数据
        $account = $this->createAccount();
        $template = new Template();
        $template->setTplId('tpl001');
        $template->setContent('旧内容');
        $template->setAccount($account);
        
        $newContent = '新内容';
        
        $apiResponse = [
            'tpl_id' => 'tpl001',
            'status' => 'SUCCESS'
        ];
        
        // 设置模拟对象预期行为
        $this->apiClient->expects($this->once())
            ->method('requestArray')
            ->willReturnCallback(function ($request) use ($apiResponse, $newContent) {
                $this->assertInstanceOf(UpdateTemplateRequest::class, $request);
                $this->assertEquals('tpl001', $request->getTplId());
                $this->assertEquals($newContent, $request->getContent());
                return $apiResponse;
            });
            
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // 执行测试
        $result = $this->templateService->update($template, $newContent);
        
        // 断言结果
        $this->assertSame($template, $result);
        $this->assertEquals($newContent, $result->getContent());
    }
    
    public function testDelete_withValidTemplate(): void
    {
        // 准备测试数据
        $account = $this->createAccount();
        $template = new Template();
        $template->setTplId('tpl001');
        $template->setAccount($account);
        
        $apiResponse = [
            'status' => 'SUCCESS'
        ];
        
        // 设置模拟对象预期行为
        $this->apiClient->expects($this->once())
            ->method('requestArray')
            ->willReturnCallback(function ($request) use ($apiResponse) {
                $this->assertInstanceOf(DeleteTemplateRequest::class, $request);
                $this->assertEquals('tpl001', $request->getTemplateId());
                return $apiResponse;
            });
            
        $this->entityManager->expects($this->once())
            ->method('remove')
            ->with($template);
            
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // 执行测试
        $result = $this->templateService->delete($template);
        
        // 断言结果
        $this->assertTrue($result);
    }
    
    public function testDelete_withApiError(): void
    {
        // 准备测试数据
        $account = $this->createAccount();
        $template = new Template();
        $template->setTplId('tpl001');
        $template->setAccount($account);
        
        // 设置模拟对象预期行为
        $this->apiClient->expects($this->once())
            ->method('requestArray')
            ->willThrowException(new \Exception('API错误'));
            
        $this->logger->expects($this->once())
            ->method('error')
            ->with('删除模板失败: {message}', $this->anything());
            
        $this->entityManager->expects($this->never())
            ->method('remove');
            
        $this->entityManager->expects($this->never())
            ->method('flush');
        
        // 执行测试
        $result = $this->templateService->delete($template);
        
        // 断言结果
        $this->assertFalse($result);
    }

    // 辅助方法：创建测试用的Account实例
    private function createAccount(): Account
    {
        return \YunpianSmsBundle\Tests\Mock\MockHelper::createAccount();
    }
} 