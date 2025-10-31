<?php

namespace YunpianSmsBundle\Tests\Request\Sign;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Request\Sign\GetSignRequest;

/**
 * @internal
 */
#[CoversClass(GetSignRequest::class)]
final class GetSignRequestTest extends TestCase
{
    public function testGetRequestPath(): void
    {
        $request = new GetSignRequest();
        $this->assertEquals('https://sms.yunpian.com/v2/sign/get.json', $request->getRequestPath());
    }

    public function testGetRequestMethod(): void
    {
        $request = new GetSignRequest();
        $this->assertEquals('POST', $request->getRequestMethod());
    }

    public function testGetRequestOptions(): void
    {
        $request = new GetSignRequest();

        $account = new Account();
        $account->setApiKey('test-api-key');
        $request->setAccount($account);

        /** @var array<string, mixed> $options */
        $options = $request->getRequestOptions();
        $this->assertIsArray($options);
        $this->assertArrayHasKey('body', $options);

        /** @var string $body */
        $body = $options['body'];
        $this->assertStringContainsString('apikey=test-api-key', $body);
    }

    public function testGetRequestOptionsWithSignId(): void
    {
        $request = new GetSignRequest();

        $account = new Account();
        $account->setApiKey('test-api-key');
        $request->setAccount($account);

        $signId = 1001;
        $request->setSignId($signId);

        /** @var array<string, mixed> $options */
        $options = $request->getRequestOptions();
        $this->assertIsArray($options);
        $this->assertArrayHasKey('body', $options);

        /** @var string $body */
        $body = $options['body'];
        $this->assertStringContainsString('apikey=test-api-key', $body);
        $this->assertStringContainsString('sign_id=1001', $body);
    }
}
