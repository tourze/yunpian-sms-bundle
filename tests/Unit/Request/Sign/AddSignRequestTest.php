<?php

namespace YunpianSmsBundle\Tests\Unit\Request\Sign;

use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Request\Sign\AddSignRequest;

class AddSignRequestTest extends TestCase
{
    public function testRequestCanBeInstantiated(): void
    {
        $request = new AddSignRequest();
        $this->assertInstanceOf(AddSignRequest::class, $request);
    }
}