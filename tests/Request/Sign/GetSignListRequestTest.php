<?php

namespace YunpianSmsBundle\Tests\Request\Sign;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Request\Sign\GetSignListRequest;

/**
 * @internal
 */
#[CoversClass(GetSignListRequest::class)]
final class GetSignListRequestTest extends TestCase
{
    public function testGetMethod(): void
    {
        $request = new GetSignListRequest();
        $this->assertEquals('POST', $request->getMethod());
    }

    public function testGetUri(): void
    {
        $request = new GetSignListRequest();
        $this->assertEquals('https://sms.yunpian.com/v2/sign/get.json', $request->getUri());
    }
}
