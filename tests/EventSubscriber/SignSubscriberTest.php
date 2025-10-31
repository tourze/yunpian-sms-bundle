<?php

namespace YunpianSmsBundle\Tests\EventSubscriber;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractEventSubscriberTestCase;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\Sign;
use YunpianSmsBundle\EventSubscriber\SignSubscriber;

/**
 * @internal
 */
#[CoversClass(SignSubscriber::class)]
#[RunTestsInSeparateProcesses]
final class SignSubscriberTest extends AbstractEventSubscriberTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testSubscriberCanBeInstantiated(): void
    {
        $subscriber = self::getService(SignSubscriber::class);
        $this->assertInstanceOf(SignSubscriber::class, $subscriber);
    }

    public function testPrePersistInTestEnvironment(): void
    {
        $subscriber = self::getService(SignSubscriber::class);

        $account = new Account();
        $account->setApiKey('test-key');
        $account->setRemark('Test Account');

        $sign = new Sign();
        $sign->setAccount($account);
        $sign->setSignId(1);
        $sign->setWebsite('https://test.example.com');
        $sign->setSign('测试签名');

        // 在测试环境下应该不会调用SignService，直接返回
        $subscriber->prePersist($sign);

        // 验证sign对象没有被修改
        $this->assertEquals($account, $sign->getAccount());
        $this->assertEquals(1, $sign->getSignId());
        $this->assertEquals('https://test.example.com', $sign->getWebsite());
        $this->assertEquals('测试签名', $sign->getSign());
    }

    public function testPreUpdateInTestEnvironment(): void
    {
        $subscriber = self::getService(SignSubscriber::class);

        $account = new Account();
        $account->setApiKey('test-key');
        $account->setRemark('Test Account');

        $sign = new Sign();
        $sign->setAccount($account);
        $sign->setSignId(1);
        $sign->setWebsite('https://updated.example.com');
        $sign->setSign('更新的测试签名');

        // 在测试环境下应该不会调用SignService，直接返回
        $subscriber->preUpdate($sign);

        // 验证sign对象没有被修改
        $this->assertEquals($account, $sign->getAccount());
        $this->assertEquals(1, $sign->getSignId());
        $this->assertEquals('https://updated.example.com', $sign->getWebsite());
        $this->assertEquals('更新的测试签名', $sign->getSign());
    }

    public function testPreRemoveInTestEnvironment(): void
    {
        $subscriber = self::getService(SignSubscriber::class);

        $account = new Account();
        $account->setApiKey('test-key');
        $account->setRemark('Test Account');

        $sign = new Sign();
        $sign->setAccount($account);
        $sign->setSignId(1);
        $sign->setWebsite('https://remove.example.com');
        $sign->setSign('要删除的测试签名');

        // 在测试环境下应该不会调用SignService，直接返回
        $subscriber->preRemove($sign);

        // 验证sign对象没有被修改
        $this->assertEquals($account, $sign->getAccount());
        $this->assertEquals(1, $sign->getSignId());
        $this->assertEquals('https://remove.example.com', $sign->getWebsite());
        $this->assertEquals('要删除的测试签名', $sign->getSign());
    }

    // 集成测试中无法直接mock服务依赖，复杂的交互逻辑应该在单元测试中验证
}
