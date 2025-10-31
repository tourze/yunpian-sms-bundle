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
final class SimpleSendSmsRequestTest extends TestCase
{
    public function testGetRequestPath(): void
    {
        $request = new SendSmsRequest();
        $this->assertEquals('https://sms.yunpian.com/v2/sms/single_send.json', $request->getRequestPath());
    }

    public function testGetRequestMethod(): void
    {
        $request = new SendSmsRequest();
        $this->assertEquals('POST', $request->getRequestMethod());
    }

    public function testGetRequestOptions(): void
    {
        $request = new SendSmsRequest();

        $account = new Account();
        $account->setApiKey('test-api-key');
        $request->setAccount($account);

        $mobile = '13800138000';
        $request->setMobile($mobile);

        $content = '测试短信内容';
        $request->setContent($content);

        $options = $request->getRequestOptions();
        $this->assertIsArray($options);
        $this->assertArrayHasKey('body', $options);

        $body = $options['body'];
        $this->assertIsString($body);

        $this->assertStringContainsString('apikey=test-api-key', $body);
        $this->assertStringContainsString('mobile=13800138000', $body);
        $this->assertStringContainsString('text=', $body);
    }
}
