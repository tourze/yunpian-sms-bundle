<?php

namespace YunpianSmsBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;
use YunpianSmsBundle\DependencyInjection\YunpianSmsExtension;
use YunpianSmsBundle\Service\SmsApiClient;

/**
 * @internal
 */
#[CoversClass(YunpianSmsExtension::class)]
final class YunpianSmsExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    public function testLoadExtension(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.environment', 'test');
        $extension = new YunpianSmsExtension();

        $extension->load([], $container);

        // 验证扩展加载后container中有相关服务定义
        $this->assertTrue($container->hasDefinition(SmsApiClient::class) || $container->hasAlias(SmsApiClient::class));
    }
}
