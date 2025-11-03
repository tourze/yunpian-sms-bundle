<?php

namespace YunpianSmsBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use YunpianSmsBundle\Enum\SendStatusEnum;

/**
 * @internal
 */
#[CoversClass(SendStatusEnum::class)]
final class SendStatusEnumTest extends AbstractEnumTestCase
{
    public function testEnumValues(): void
    {
        $this->assertEquals('pending', SendStatusEnum::PENDING->value);
        $this->assertEquals('sending', SendStatusEnum::SENDING->value);
        $this->assertEquals('success', SendStatusEnum::SUCCESS->value);
        $this->assertEquals('failed', SendStatusEnum::FAILED->value);
        $this->assertEquals('delivered', SendStatusEnum::DELIVERED->value);
        $this->assertEquals('undelivered', SendStatusEnum::UNDELIVERED->value);
    }

    public function testGetLabel(): void
    {
        $this->assertEquals('待发送', SendStatusEnum::PENDING->getLabel());
        $this->assertEquals('发送中', SendStatusEnum::SENDING->getLabel());
        $this->assertEquals('发送成功', SendStatusEnum::SUCCESS->getLabel());
        $this->assertEquals('发送失败', SendStatusEnum::FAILED->getLabel());
        $this->assertEquals('已送达', SendStatusEnum::DELIVERED->getLabel());
        $this->assertEquals('未送达', SendStatusEnum::UNDELIVERED->getLabel());
    }

    public function testGetChoices(): void
    {
        $choices = SendStatusEnum::getChoices();

        $this->assertArrayHasKey('待发送', $choices);
        $this->assertArrayHasKey('发送中', $choices);
        $this->assertArrayHasKey('发送成功', $choices);
        $this->assertArrayHasKey('发送失败', $choices);
        $this->assertArrayHasKey('已送达', $choices);
        $this->assertArrayHasKey('未送达', $choices);

        $this->assertEquals(SendStatusEnum::PENDING, $choices['待发送']);
        $this->assertEquals(SendStatusEnum::SENDING, $choices['发送中']);
        $this->assertEquals(SendStatusEnum::SUCCESS, $choices['发送成功']);
        $this->assertEquals(SendStatusEnum::FAILED, $choices['发送失败']);
        $this->assertEquals(SendStatusEnum::DELIVERED, $choices['已送达']);
        $this->assertEquals(SendStatusEnum::UNDELIVERED, $choices['未送达']);
    }

    public function testToArray(): void
    {
        $array = SendStatusEnum::PENDING->toArray();
        // 移除冗余断言：toArray() 的返回类型已在 ItemTrait 中声明为 array
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertEquals('pending', $array['value']);
        $this->assertEquals('待发送', $array['label']);
    }
}
