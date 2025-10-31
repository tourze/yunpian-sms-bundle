<?php

namespace YunpianSmsBundle\Tests\Request;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Request\SendSmsRequest;

/**
 * @internal
 */
#[CoversClass(SendSmsRequest::class)]
final class SendSmsRequestTest extends TestCase
{
    private SendSmsRequest $request;

    private Account $account;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new SendSmsRequest();

        $this->account = new Account();
        $this->account->setApiKey('test-api-key');
        $this->request->setAccount($this->account);
    }

    public function testGetRequestPath(): void
    {
        $this->assertEquals('https://sms.yunpian.com/v2/sms/single_send.json', $this->request->getRequestPath());
    }

    public function testGetRequestMethod(): void
    {
        $this->assertEquals('POST', $this->request->getRequestMethod());
    }

    public function testGetRequestOptionsWithRequiredFields(): void
    {
        // 准备测试数据
        $mobile = '13800138000';
        $content = '您的验证码是1234';

        // 设置请求参数
        $this->request->setMobile($mobile);
        $this->request->setContent($content);

        // 获取请求选项
        /** @var array<string, mixed> $options */
        $options = $this->request->getRequestOptions();

        // 断言结果
        $this->assertIsArray($options);
        $this->assertArrayHasKey('body', $options);
        $this->assertArrayHasKey('headers', $options);

        /** @var string $body */
        $body = $options['body'];
        $this->assertIsString($body);

        // 验证body包含必要的参数
        $this->assertStringContainsString('apikey=test-api-key', $body);
        $this->assertStringContainsString('mobile=13800138000', $body);
        $this->assertStringContainsString('text=', $body);

        // 验证Content-Type header
        /** @var array<string, string> $headers */
        $headers = $options['headers'];
        $this->assertEquals('application/x-www-form-urlencoded', $headers['Content-Type']);
    }

    public function testGetRequestOptionsWithUid(): void
    {
        // 准备测试数据
        $mobile = '13800138000';
        $content = '您的验证码是1234';
        $uid = 'test-uid';

        // 设置请求参数
        $this->request->setMobile($mobile);
        $this->request->setContent($content);
        $this->request->setUid($uid);

        // 获取请求选项
        /** @var array<string, mixed> $options */
        $options = $this->request->getRequestOptions();

        // 断言结果
        $this->assertIsArray($options);
        $this->assertArrayHasKey('body', $options);

        /** @var string $body */
        $body = $options['body'];
        $this->assertStringContainsString('apikey=test-api-key', $body);
        $this->assertStringContainsString('mobile=13800138000', $body);
        $this->assertStringContainsString('text=', $body);
        $this->assertStringContainsString('uid=test-uid', $body);
    }

    public function testGetRequestOptionsWithMultipleMobiles(): void
    {
        // 准备测试数据
        $mobile = '13800138000,13900139000';
        $content = '您的验证码是1234';

        // 设置请求参数
        $this->request->setMobile($mobile);
        $this->request->setContent($content);

        // 获取请求选项
        /** @var array<string, mixed> $options */
        $options = $this->request->getRequestOptions();

        // 断言结果
        $this->assertIsArray($options);
        $this->assertArrayHasKey('body', $options);

        /** @var string $body */
        $body = $options['body'];
        $this->assertStringContainsString('mobile=13800138000%2C13900139000', $body);
    }

    public function testGetRequestOptionsWithLongContent(): void
    {
        // 准备测试数据
        $mobile = '13800138000';
        $content = str_repeat('测试内容', 100); // 创建一个长内容

        // 设置请求参数
        $this->request->setMobile($mobile);
        $this->request->setContent($content);

        // 获取请求选项
        /** @var array<string, mixed> $options */
        $options = $this->request->getRequestOptions();

        // 断言结果
        $this->assertIsArray($options);
        $this->assertArrayHasKey('body', $options);

        /** @var string $body */
        $body = $options['body'];
        $this->assertStringContainsString('text=', $body);
        // 验证内容已被正确编码
        $this->assertStringContainsString(urlencode($content), $body);
    }
}
