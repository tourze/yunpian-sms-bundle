<?php

namespace YunpianSmsBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use YunpianSmsBundle\Entity\Account;

/**
 * @internal
 */
#[CoversClass(Account::class)]
final class AccountTest extends AbstractEntityTestCase
{
    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'apiKey' => ['apiKey', 'test-api-key-123'];
        yield 'valid' => ['valid', true];
        yield 'remark' => ['remark', '测试账号备注'];
    }

    protected function createEntity(): Account
    {
        return new Account();
    }

    public function testIdGetter(): void
    {
        $account = new Account();
        $this->assertEquals(0, $account->getId());
    }

    public function testDefaultValuesAreSetCorrectly(): void
    {
        $account = new Account();
        $this->assertNull($account->getCreateTime());
        $this->assertNull($account->getUpdateTime());
        $this->assertEquals(0, $account->getId());
    }

    public function testConstructor(): void
    {
        $account = new Account();

        // 验证默认值
        $this->assertEquals(0, $account->getId());
        $this->assertFalse($account->isValid());
        $this->assertNull($account->getRemark());
    }

    public function testSetAndGetApiKey(): void
    {
        $account = new Account();

        $apiKey = 'test-api-key-123';
        $account->setApiKey($apiKey);

        $this->assertSame($apiKey, $account->getApiKey());
    }

    public function testSetAndGetValid(): void
    {
        $account = new Account();
        $this->assertFalse($account->isValid());

        $account->setValid(true);
        $this->assertTrue($account->isValid());

        $account->setValid(false);
        $this->assertFalse($account->isValid());
    }

    public function testSetAndGetRemark(): void
    {
        $account = new Account();
        $this->assertNull($account->getRemark());

        $remark = '这是一个测试账号';
        $account->setRemark($remark);

        $this->assertSame($remark, $account->getRemark());

        // 测试空字符串
        $account->setRemark('');
        $this->assertSame('', $account->getRemark());

        // 测试重置为null
        $account->setRemark(null);
        $this->assertNull($account->getRemark());
    }

    public function testSetAndGetCreateTime(): void
    {
        $account = new Account();

        $now = new \DateTimeImmutable();
        $account->setCreateTime($now);

        $this->assertSame($now, $account->getCreateTime());
    }

    public function testSetAndGetUpdateTime(): void
    {
        $account = new Account();

        $now = new \DateTimeImmutable();
        $account->setUpdateTime($now);

        $this->assertSame($now, $account->getUpdateTime());
    }
}
