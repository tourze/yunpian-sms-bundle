<?php

namespace YunpianSmsBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpClient\Exception\ClientException;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\SendLog;
use YunpianSmsBundle\Entity\Template;
use YunpianSmsBundle\Service\SendLogService;

/**
 * @internal
 */
#[CoversClass(SendLogService::class)]
#[RunTestsInSeparateProcesses]
final class SendLogServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testSend(): void
    {
        $service = self::getService(SendLogService::class);
        $entityManager = self::getService(EntityManagerInterface::class);

        $account = new Account();
        $account->setApiKey('test-key');
        $account->setRemark('Test Account');
        $entityManager->persist($account);
        $entityManager->flush();

        $sendLog = $service->create($account, '13800138000', 'Test message');

        $this->assertInstanceOf(SendLog::class, $sendLog);
        $this->assertSame($account, $sendLog->getAccount());
        $this->assertSame('13800138000', $sendLog->getMobile());
        $this->assertSame('Test message', $sendLog->getContent());
    }

    public function testSendWithoutUid(): void
    {
        $service = self::getService(SendLogService::class);
        $entityManager = self::getService(EntityManagerInterface::class);

        $account = new Account();
        $account->setApiKey('test-key-2');
        $account->setRemark('Test Account 2');
        $entityManager->persist($account);
        $entityManager->flush();

        $sendLog = $service->create($account, '13900139000', 'Another test message', null, null);

        $this->assertInstanceOf(SendLog::class, $sendLog);
        $this->assertNull($sendLog->getUid());
        $this->assertNull($sendLog->getTemplate());
    }

    public function testSendMethodThrowsExceptionWithInvalidApiKey(): void
    {
        $service = self::getService(SendLogService::class);
        $entityManager = self::getService(EntityManagerInterface::class);

        $account = new Account();
        $account->setApiKey('test-send-key');
        $account->setRemark('Test Send Account');
        $entityManager->persist($account);
        $entityManager->flush();

        // send方法会尝试发送短信，在测试环境下应该抛出API错误异常
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('请检查您的apikey是否正确，或者账户已经失效');

        $service->send($account, '13800138000', 'Test send message', 'test-uid');
    }

    public function testSendTplMethodThrowsExceptionWithInvalidApiKey(): void
    {
        $service = self::getService(SendLogService::class);
        $entityManager = self::getService(EntityManagerInterface::class);

        $account = new Account();
        $account->setApiKey('test-tpl-key');
        $account->setRemark('Test Template Account');
        $entityManager->persist($account);

        $template = new Template();
        $template->setAccount($account);
        $template->setTplId('test-template-key');
        $template->setTitle('Test Template');
        $template->setContent('您的验证码是{code}');
        $template->setCheckStatus('CHECKING');
        $entityManager->persist($template);
        $entityManager->flush();

        $tplValue = ['code' => '1234'];

        // sendTpl方法会尝试发送模板短信，在测试环境下应该抛出API错误异常
        $this->expectException(ClientException::class);

        $service->sendTpl($account, $template, '13800138000', $tplValue, 'test-tpl-uid');
    }

    public function testCreateWithTemplate(): void
    {
        $service = self::getService(SendLogService::class);
        $entityManager = self::getService(EntityManagerInterface::class);

        $account = new Account();
        $account->setApiKey('test-create-key');
        $account->setRemark('Test Create Account');
        $entityManager->persist($account);

        $template = new Template();
        $template->setAccount($account);
        $template->setTplId('test-create-template-key');
        $template->setTitle('Test Create Template');
        $template->setContent('创建的模板{code}');
        $template->setCheckStatus('CHECKING');
        $entityManager->persist($template);
        $entityManager->flush();

        $sendLog = $service->create($account, '13700137000', 'Create test message', $template, 'create-uid');

        $this->assertInstanceOf(SendLog::class, $sendLog);
        $this->assertEquals($account, $sendLog->getAccount());
        $this->assertEquals($template, $sendLog->getTemplate());
        $this->assertEquals('13700137000', $sendLog->getMobile());
        $this->assertEquals('Create test message', $sendLog->getContent());
        $this->assertEquals('create-uid', $sendLog->getUid());
    }

    public function testSyncStatusDoesNotThrowException(): void
    {
        $service = self::getService(SendLogService::class);

        // syncStatus方法应该可以执行而不抛出异常（即使在测试环境下可能没有实际数据）
        $service->syncStatus();

        // 如果没有异常抛出，测试就通过了
        $this->assertTrue(true);
    }

    public function testSyncRecordThrowsExceptionWithInvalidApiKey(): void
    {
        $service = self::getService(SendLogService::class);
        $entityManager = self::getService(EntityManagerInterface::class);

        $account = new Account();
        $account->setApiKey('test-sync-key');
        $account->setRemark('Test Sync Account');
        $entityManager->persist($account);
        $entityManager->flush();

        $startTime = new \DateTime('2024-01-01');
        $endTime = new \DateTime('2024-01-02');

        // syncRecord方法会尝试调用API，在测试环境下应该抛出API错误异常
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('请检查您的apikey是否正确，或者账户已经失效');

        $service->syncRecord($account, $startTime, $endTime, '13800138000');
    }
}
