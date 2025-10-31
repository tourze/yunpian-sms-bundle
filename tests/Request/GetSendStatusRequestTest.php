<?php

namespace YunpianSmsBundle\Tests\Request;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Request\GetSendStatusRequest;

/**
 * @internal
 */
#[CoversClass(GetSendStatusRequest::class)]
final class GetSendStatusRequestTest extends TestCase
{
    public function testGetRequestPath(): void
    {
        $request = new GetSendStatusRequest();
        $this->assertEquals('https://sms.yunpian.com/v2/sms/pull_status.json', $request->getRequestPath());
    }

    public function testGetRequestMethod(): void
    {
        $request = new GetSendStatusRequest();
        $this->assertEquals('POST', $request->getRequestMethod());
    }

    public function testGetRequestOptions(): void
    {
        $request = new GetSendStatusRequest();

        $account = new Account();
        $account->setApiKey('test-api-key');
        $request->setAccount($account);

        $sids = ['1001', '1002', '1003'];
        $request->setSids($sids);

        /** @var array<string, mixed> $options */
        $options = $request->getRequestOptions();
        $this->assertArrayHasKey('body', $options);
        $this->assertArrayHasKey('headers', $options);

        /** @var string $body */
        $body = $options['body'];
        $this->assertIsString($body);

        // 验证body包含必要的参数
        $this->assertStringContainsString('apikey=test-api-key', $body);
        $this->assertStringContainsString('sid=1001%2C1002%2C1003', $body);

        // 验证Content-Type header
        /** @var array<string, string> $headers */
        $headers = $options['headers'];
        $this->assertArrayHasKey('Content-Type', $headers);
        $this->assertEquals('application/x-www-form-urlencoded', $headers['Content-Type']);
    }

    public function testGetSids(): void
    {
        $request = new GetSendStatusRequest();

        $sids = ['1001', '1002', '1003'];
        $request->setSids($sids);

        $this->assertEquals($sids, $request->getSids());
    }
}
