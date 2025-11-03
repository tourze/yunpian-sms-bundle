<?php

namespace YunpianSmsBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use YunpianSmsBundle\Enum\NotifyTypeEnum;

/**
 * @internal
 */
#[CoversClass(NotifyTypeEnum::class)]
final class NotifyTypeEnumTest extends AbstractEnumTestCase
{
    public function testEnumValues(): void
    {
        $this->assertEquals(0, NotifyTypeEnum::ALWAYS->value);
        $this->assertEquals(1, NotifyTypeEnum::ONLY_FAILED->value);
        $this->assertEquals(2, NotifyTypeEnum::ONLY_SUCCESS->value);
        $this->assertEquals(3, NotifyTypeEnum::NEVER->value);
    }

    public function testGetLabel(): void
    {
        $this->assertEquals('始终通知', NotifyTypeEnum::ALWAYS->getLabel());
        $this->assertEquals('仅审核不通过时通知', NotifyTypeEnum::ONLY_FAILED->getLabel());
        $this->assertEquals('仅审核通过时通知', NotifyTypeEnum::ONLY_SUCCESS->getLabel());
        $this->assertEquals('不通知', NotifyTypeEnum::NEVER->getLabel());
    }

    public function testGetChoices(): void
    {
        $choices = NotifyTypeEnum::getChoices();

        $this->assertArrayHasKey('始终通知', $choices);
        $this->assertArrayHasKey('仅审核不通过时通知', $choices);
        $this->assertArrayHasKey('仅审核通过时通知', $choices);
        $this->assertArrayHasKey('不通知', $choices);

        $this->assertEquals(NotifyTypeEnum::ALWAYS, $choices['始终通知']);
        $this->assertEquals(NotifyTypeEnum::ONLY_FAILED, $choices['仅审核不通过时通知']);
        $this->assertEquals(NotifyTypeEnum::ONLY_SUCCESS, $choices['仅审核通过时通知']);
        $this->assertEquals(NotifyTypeEnum::NEVER, $choices['不通知']);
    }

    public function testToArray(): void
    {
        $array = NotifyTypeEnum::ALWAYS->toArray();
        // 移除冗余断言：toArray() 的返回类型已在 ItemTrait 中声明为 array
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertEquals(0, $array['value']);
        $this->assertEquals('始终通知', $array['label']);
    }
}
