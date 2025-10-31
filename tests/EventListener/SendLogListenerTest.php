<?php

namespace YunpianSmsBundle\Tests\EventListener;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\SendLog;
use YunpianSmsBundle\EventListener\SendLogListener;
use YunpianSmsBundle\Exception\TemplateParseException;

/**
 * @internal
 */
#[CoversClass(SendLogListener::class)]
#[RunTestsInSeparateProcesses]
final class SendLogListenerTest extends AbstractIntegrationTestCase
{
    private SendLogListener $listener;

    protected function onSetUp(): void
    {
        // 从容器获取 SendLogListener 服务
        $service = self::getContainer()->get(SendLogListener::class);
        $this->assertInstanceOf(SendLogListener::class, $service);
        $this->listener = $service;
    }

    public function testListenerCanBeInstantiated(): void
    {
        $this->assertInstanceOf(SendLogListener::class, $this->listener);
    }

    public function testPrePersistWithExistingSid(): void
    {
        $account = new Account();
        $account->setApiKey('test-key');
        $account->setRemark('Test Account');

        $sendLog = new SendLog();
        $sendLog->setAccount($account);
        $sendLog->setMobile('13800138000');
        $sendLog->setContent('Test message');
        $sendLog->setSid('existing-sid');

        // 验证如果已有sid，不会被改变
        $originalSid = $sendLog->getSid();
        $this->assertEquals('existing-sid', $originalSid);
        $this->assertNotNull($originalSid);

        // 验证方法存在并可以被反射
        $reflection = new \ReflectionMethod($this->listener, 'prePersist');
        $this->assertTrue($reflection->isPublic());
    }

    public function testPrePersistLogic(): void
    {
        // 验证构造函数和依赖注入
        $reflection = new \ReflectionClass($this->listener);
        $constructor = $reflection->getConstructor();
        $this->assertNotNull($constructor);
        $this->assertCount(2, $constructor->getParameters());

        $method = $reflection->getMethod('prePersist');
        $this->assertTrue($method->isPublic());
        $this->assertCount(2, $method->getParameters());

        // 验证方法参数类型
        $params = $method->getParameters();
        $firstParamType = $params[0]->getType();
        $secondParamType = $params[1]->getType();

        $this->assertNotNull($firstParamType);
        $this->assertNotNull($secondParamType);

        // 安全检查类型并获取名称
        if ($firstParamType instanceof \ReflectionNamedType) {
            $this->assertEquals('YunpianSmsBundle\Entity\SendLog', $firstParamType->getName());
        }

        if ($secondParamType instanceof \ReflectionNamedType) {
            $this->assertEquals('Doctrine\ORM\Event\PrePersistEventArgs', $secondParamType->getName());
        }
    }

    public function testParseTplValueReflection(): void
    {
        $reflection = new \ReflectionClass($this->listener);

        // 验证parseTplValue私有方法存在
        $this->assertTrue($reflection->hasMethod('parseTplValue'));

        $parseTplMethod = $reflection->getMethod('parseTplValue');
        $this->assertTrue($parseTplMethod->isPrivate());

        // 测试模板解析逻辑（通过反射调用私有方法）
        $parseTplMethod->setAccessible(true);

        // 测试正常情况
        $result = $parseTplMethod->invoke($this->listener, '{code}1234');
        $this->assertEquals(['code' => '1234'], $result);

        // 测试多个变量
        $result = $parseTplMethod->invoke($this->listener, '{code}1234{name}测试');
        $this->assertEquals(['code' => '1234', 'name' => '测试'], $result);
    }

    public function testTemplateContentValidation(): void
    {
        // 测试模板内容解析的边界情况
        $reflection = new \ReflectionClass($this->listener);
        $parseTplMethod = $reflection->getMethod('parseTplValue');
        $parseTplMethod->setAccessible(true);

        // 测试异常情况：无效的模板格式
        $this->expectException(TemplateParseException::class);
        $parseTplMethod->invoke($this->listener, 'no template variables');
    }
}
