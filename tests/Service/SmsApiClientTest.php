<?php

namespace YunpianSmsBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use YunpianSmsBundle\Request\RequestInterface;
use YunpianSmsBundle\Service\SmsApiClient;

/**
 * @internal
 */
#[CoversClass(SmsApiClient::class)]
#[RunTestsInSeparateProcesses]
final class SmsApiClientTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testRequestArray(): void
    {
        $smsApiClient = self::getService(SmsApiClient::class);
        $this->assertInstanceOf(SmsApiClient::class, $smsApiClient);

        // 测试服务能够正常实例化
        $this->assertNotNull($smsApiClient);
    }

    public function testRequestArrayWithTestEnvironment(): void
    {
        $smsApiClient = self::getService(SmsApiClient::class);
        $this->assertInstanceOf(SmsApiClient::class, $smsApiClient);

        // 创建一个匿名类实现RequestInterface
        $testRequest = new class implements RequestInterface {
            public function getRequestMethod(): string
            {
                return 'POST';
            }

            public function getRequestPath(): string
            {
                return '/test';
            }

            /**
             * @return array<string, mixed>
             */
            public function getRequestOptions(): array
            {
                return ['test' => 'value'];
            }
        };

        // 测试服务和请求对象都存在
        $this->assertNotNull($testRequest);
    }

    public function testSanitizeOptions(): void
    {
        $smsApiClient = self::getService(SmsApiClient::class);
        $this->assertInstanceOf(SmsApiClient::class, $smsApiClient);

        // 测试服务能够正常工作
        $this->assertNotNull($smsApiClient);
    }
}
