<?php

namespace YunpianSmsBundle\Tests\Request\Sign;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Request\Sign\DeleteSignRequest;

/**
 * @internal
 */
#[CoversClass(DeleteSignRequest::class)]
final class DeleteSignRequestTest extends TestCase
{
    public function testGetMethod(): void
    {
        $request = new DeleteSignRequest();
        $this->assertEquals('POST', $request->getMethod());
    }

    public function testGetUri(): void
    {
        $request = new DeleteSignRequest();
        $this->assertEquals('https://sms.yunpian.com/v2/sign/del.json', $request->getUri());
    }

    public function testGetBodyWithSignId(): void
    {
        $account = new Account();
        $account->setApiKey('test-api-key');

        $request = new DeleteSignRequest();
        $request->setAccount($account);
        $request->setSignId(123);

        $body = $request->getBody();

        $this->assertEquals('test-api-key', $body['apikey']);
        $this->assertEquals(123, $body['sign_id']);
        $this->assertArrayNotHasKey('sign', $body);
    }

    public function testGetBodyWithSign(): void
    {
        $account = new Account();
        $account->setApiKey('test-api-key');

        $request = new DeleteSignRequest();
        $request->setAccount($account);
        $request->setSign('TestSign');

        $body = $request->getBody();

        $this->assertEquals('test-api-key', $body['apikey']);
        $this->assertEquals('TestSign', $body['sign']);
        $this->assertArrayNotHasKey('sign_id', $body);
    }
}
