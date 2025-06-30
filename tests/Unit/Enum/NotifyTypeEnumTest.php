<?php

namespace YunpianSmsBundle\Tests\Unit\Enum;

use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Enum\NotifyTypeEnum;

class NotifyTypeEnumTest extends TestCase
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
}