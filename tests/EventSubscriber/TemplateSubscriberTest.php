<?php

namespace YunpianSmsBundle\Tests\EventSubscriber;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractEventSubscriberTestCase;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\Template;
use YunpianSmsBundle\EventSubscriber\TemplateSubscriber;

/**
 * @internal
 */
#[CoversClass(TemplateSubscriber::class)]
#[RunTestsInSeparateProcesses]
final class TemplateSubscriberTest extends AbstractEventSubscriberTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testSubscriberCanBeInstantiated(): void
    {
        $subscriber = self::getService(TemplateSubscriber::class);
        $this->assertInstanceOf(TemplateSubscriber::class, $subscriber);
    }

    public function testPrePersistInTestEnvironment(): void
    {
        $subscriber = self::getService(TemplateSubscriber::class);

        $account = new Account();
        $account->setApiKey('test-key');
        $account->setRemark('Test Account');

        $template = new Template();
        $template->setAccount($account);
        $template->setTplId('test-template-key');
        $template->setTitle('Test Template');
        $template->setContent('您的验证码是{code}');
        $template->setCheckStatus('CHECKING');

        // 在测试环境下应该不会调用TemplateService，直接返回
        $subscriber->prePersist($template);

        // 验证template对象没有被修改
        $this->assertEquals($account, $template->getAccount());
        $this->assertEquals('test-template-key', $template->getTplId());
        $this->assertEquals('Test Template', $template->getTitle());
        $this->assertEquals('您的验证码是{code}', $template->getContent());
    }

    public function testPreUpdateInTestEnvironment(): void
    {
        $subscriber = self::getService(TemplateSubscriber::class);

        $account = new Account();
        $account->setApiKey('test-key');
        $account->setRemark('Test Account');

        $template = new Template();
        $template->setAccount($account);
        $template->setTplId('test-template-key');
        $template->setTitle('Updated Test Template');
        $template->setContent('您的新验证码是{code}');
        $template->setCheckStatus('CHECKING');

        // 在测试环境下应该不会调用TemplateService，直接返回
        $subscriber->preUpdate($template);

        // 验证template对象没有被修改
        $this->assertEquals($account, $template->getAccount());
        $this->assertEquals('test-template-key', $template->getTplId());
        $this->assertEquals('Updated Test Template', $template->getTitle());
        $this->assertEquals('您的新验证码是{code}', $template->getContent());
    }

    public function testPreRemoveInTestEnvironment(): void
    {
        $subscriber = self::getService(TemplateSubscriber::class);

        $account = new Account();
        $account->setApiKey('test-key');
        $account->setRemark('Test Account');

        $template = new Template();
        $template->setAccount($account);
        $template->setTplId('test-template-key');
        $template->setTitle('Template to Remove');
        $template->setContent('要删除的模板{code}');
        $template->setCheckStatus('CHECKING');

        // 在测试环境下应该不会调用TemplateService，直接返回
        $subscriber->preRemove($template);

        // 验证template对象没有被修改
        $this->assertEquals($account, $template->getAccount());
        $this->assertEquals('test-template-key', $template->getTplId());
        $this->assertEquals('Template to Remove', $template->getTitle());
        $this->assertEquals('要删除的模板{code}', $template->getContent());
    }

    // 集成测试中无法直接mock服务依赖，复杂的交互逻辑应该在单元测试中验证
}
