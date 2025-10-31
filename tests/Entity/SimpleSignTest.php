<?php

namespace YunpianSmsBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\Sign;

/**
 * @internal
 */
#[CoversClass(Sign::class)]
final class SimpleSignTest extends AbstractEntityTestCase
{
    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'sign' => ['sign', 'Simple Test Signature'];
        yield 'applyState' => ['applyState', 'CHECKING'];
        yield 'valid' => ['valid', false];
        yield 'remark' => ['remark', '简单签名测试'];
    }

    protected function createEntity(): Sign
    {
        return new Sign();
    }

    public function testIdGetter(): void
    {
        $sign = new Sign();
        $this->assertEquals(0, $sign->getId());
    }

    public function testDefaultValuesAreSetCorrectly(): void
    {
        $sign = new Sign();
        $this->assertNull($sign->getCreateTime());
        $this->assertNull($sign->getUpdateTime());
        $this->assertEquals(0, $sign->getId());
    }

    public function testBasicProperties(): void
    {
        $sign = new Sign();

        $account = new Account();
        $account->setApiKey('test-api-key');

        $sign->setAccount($account);
        $sign->setSign('测试签名');
        $sign->setApplyState('SUCCESS');
        $sign->setValid(true);

        $this->assertSame($account, $sign->getAccount());
        $this->assertEquals('测试签名', $sign->getSign());
        $this->assertEquals('SUCCESS', $sign->getApplyState());
        $this->assertTrue($sign->isValid());
    }
}
