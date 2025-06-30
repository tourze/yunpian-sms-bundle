<?php

namespace YunpianSmsBundle\Tests\Unit\Request\Sign;

use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Request\Sign\DeleteSignRequest;

class DeleteSignRequestTest extends TestCase
{
    public function testRequestCanBeInstantiated(): void
    {
        $request = new DeleteSignRequest();
        $this->assertInstanceOf(DeleteSignRequest::class, $request);
    }
}