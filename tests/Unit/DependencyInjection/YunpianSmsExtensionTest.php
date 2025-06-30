<?php

namespace YunpianSmsBundle\Tests\Unit\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use YunpianSmsBundle\DependencyInjection\YunpianSmsExtension;

class YunpianSmsExtensionTest extends TestCase
{
    public function testLoadExtension(): void
    {
        $container = new ContainerBuilder();
        $extension = new YunpianSmsExtension();

        $extension->load([], $container);

        // Test that the extension loaded successfully without throwing exception
        $this->assertNotNull($container);
    }

    public function testExtensionCanBeInstantiated(): void
    {
        $extension = new YunpianSmsExtension();
        $this->assertInstanceOf(YunpianSmsExtension::class, $extension);
    }
}