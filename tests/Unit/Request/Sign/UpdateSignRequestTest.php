<?php

namespace YunpianSmsBundle\Tests\Unit\Request\Sign;

use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Request\Sign\UpdateSignRequest;

class UpdateSignRequestTest extends TestCase
{
    public function testRequestCanBeInstantiated(): void
    {
        $request = new UpdateSignRequest();
        $this->assertInstanceOf(UpdateSignRequest::class, $request);
    }
}