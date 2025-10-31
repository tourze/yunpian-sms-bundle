<?php

namespace YunpianSmsBundle\Tests\Request\Sign;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Request\Sign\UpdateSignRequest;

/**
 * @internal
 */
#[CoversClass(UpdateSignRequest::class)]
final class UpdateSignRequestTest extends TestCase
{
    public function testGetMethod(): void
    {
        $request = new UpdateSignRequest();
        $this->assertEquals('POST', $request->getMethod());
    }

    public function testGetUri(): void
    {
        $request = new UpdateSignRequest();
        $this->assertEquals('https://sms.yunpian.com/v2/sign/update.json', $request->getUri());
    }

    public function testGetHeaders(): void
    {
        $request = new UpdateSignRequest();
        $headers = $request->getHeaders();

        $this->assertArrayHasKey('Accept', $headers);
        $this->assertArrayHasKey('Content-Type', $headers);
        $this->assertEquals('application/json;charset=utf-8', $headers['Accept']);
        $this->assertEquals('application/x-www-form-urlencoded;charset=utf-8', $headers['Content-Type']);
    }

    public function testGetBodyWithSignId(): void
    {
        $account = new Account();
        $account->setApiKey('test-api-key');

        $request = new UpdateSignRequest();
        $request->setAccount($account);
        $request->setSign('TestSign');
        $request->setSignId(123);

        $body = $request->getBody();

        $this->assertEquals('test-api-key', $body['apikey']);
        $this->assertEquals('TestSign', $body['sign']);
        $this->assertEquals(123, $body['sign_id']);
        $this->assertArrayNotHasKey('old_sign', $body);
    }

    public function testGetBodyWithOldSign(): void
    {
        $account = new Account();
        $account->setApiKey('test-api-key');

        $request = new UpdateSignRequest();
        $request->setAccount($account);
        $request->setSign('NewSign');
        $request->setOldSign('OldSign');

        $body = $request->getBody();

        $this->assertEquals('test-api-key', $body['apikey']);
        $this->assertEquals('NewSign', $body['sign']);
        $this->assertEquals('OldSign', $body['old_sign']);
        $this->assertArrayNotHasKey('sign_id', $body);
    }
}
