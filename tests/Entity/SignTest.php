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
final class SignTest extends AbstractEntityTestCase
{
    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'signId' => ['signId', 123];
        yield 'sign' => ['sign', 'Test Signature'];
        yield 'applyState' => ['applyState', 'PENDING'];
        yield 'website' => ['website', 'https://example.com'];
        yield 'notify' => ['notify', true];
        yield 'applyVip' => ['applyVip', false];
        yield 'industryType' => ['industryType', 'IT'];
        yield 'proveType' => ['proveType', 1];
        yield 'licenseUrls' => ['licenseUrls', ['https://example.com/license1.jpg', 'https://example.com/license2.jpg']];
        yield 'idCardName' => ['idCardName', '张三'];
        yield 'idCardNumber' => ['idCardNumber', '1234567890'];
        yield 'idCardFront' => ['idCardFront', 'https://example.com/front.jpg'];
        yield 'idCardBack' => ['idCardBack', 'https://example.com/back.jpg'];
        yield 'signUse' => ['signUse', 0];
        yield 'valid' => ['valid', true];
        yield 'remark' => ['remark', '测试签名'];
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

    public function testEntityCanBeInstantiated(): void
    {
        $entity = new Sign();
        $this->assertInstanceOf(Sign::class, $entity);
    }

    public function testEntityWithAccount(): void
    {
        $account = new Account();
        $entity = new Sign();
        $entity->setAccount($account);

        $this->assertSame($account, $entity->getAccount());
    }

    public function testEntityWithSign(): void
    {
        $signText = 'Test Sign';
        $entity = new Sign();
        $entity->setSign($signText);

        $this->assertEquals($signText, $entity->getSign());
    }

    public function testIsOnlyGlobalProperty(): void
    {
        $entity = new Sign();

        // 测试默认值
        $this->assertFalse($entity->isOnlyGlobal());

        // 测试 setter 和 getter
        $entity->setIsOnlyGlobal(true);
        $this->assertTrue($entity->isOnlyGlobal());

        $entity->setIsOnlyGlobal(false);
        $this->assertFalse($entity->isOnlyGlobal());
    }
}
