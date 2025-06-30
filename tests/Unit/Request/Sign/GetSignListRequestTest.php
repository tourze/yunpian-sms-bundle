<?php

namespace YunpianSmsBundle\Tests\Unit\Request\Sign;

use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Request\Sign\GetSignListRequest;

class GetSignListRequestTest extends TestCase
{
    public function testRequestCanBeInstantiated(): void
    {
        $request = new GetSignListRequest();
        $this->assertInstanceOf(GetSignListRequest::class, $request);
    }
}