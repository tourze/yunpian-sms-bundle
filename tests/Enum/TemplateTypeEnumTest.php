<?php

namespace YunpianSmsBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use YunpianSmsBundle\Enum\TemplateTypeEnum;

/**
 * @internal
 */
#[CoversClass(TemplateTypeEnum::class)]
final class TemplateTypeEnumTest extends AbstractEnumTestCase
{
    public function testEnumValues(): void
    {
        $this->assertEquals(0, TemplateTypeEnum::NOTIFICATION->value);
        $this->assertEquals(1, TemplateTypeEnum::VERIFICATION->value);
    }

    public function testGetLabel(): void
    {
        $this->assertEquals('通知类模板', TemplateTypeEnum::NOTIFICATION->getLabel());
        $this->assertEquals('验证码类模板', TemplateTypeEnum::VERIFICATION->getLabel());
    }

    public function testGetChoices(): void
    {
        $choices = TemplateTypeEnum::getChoices();

        $this->assertArrayHasKey('通知类模板', $choices);
        $this->assertArrayHasKey('验证码类模板', $choices);

        $this->assertEquals(TemplateTypeEnum::NOTIFICATION, $choices['通知类模板']);
        $this->assertEquals(TemplateTypeEnum::VERIFICATION, $choices['验证码类模板']);
    }

    public function testIsVerification(): void
    {
        $this->assertTrue(TemplateTypeEnum::VERIFICATION->isVerification());
        $this->assertFalse(TemplateTypeEnum::NOTIFICATION->isVerification());
    }

    public function testIsNotification(): void
    {
        $this->assertTrue(TemplateTypeEnum::NOTIFICATION->isNotification());
        $this->assertFalse(TemplateTypeEnum::VERIFICATION->isNotification());
    }

    public function testToArray(): void
    {
        $array = TemplateTypeEnum::NOTIFICATION->toArray();
        $this->assertIsArray($array);
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertEquals(0, $array['value']);
        $this->assertEquals('通知类模板', $array['label']);
    }
}
