<?php

namespace YunpianSmsBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\YunpianSmsBundle;

class YunpianSmsBundleTest extends TestCase
{
    public function testBundleCanBeInstantiated(): void
    {
        $bundle = new YunpianSmsBundle();
        $this->assertInstanceOf(YunpianSmsBundle::class, $bundle);
    }
}