<?php

namespace YunpianSmsBundle\Tests\Request\Sign;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Request\Sign\AddSignRequest;

/**
 * @internal
 */
#[CoversClass(AddSignRequest::class)]
final class AddSignRequestTest extends TestCase
{
    public function testRequestCanBeInstantiated(): void
    {
        $request = new AddSignRequest();
        $this->assertInstanceOf(AddSignRequest::class, $request);
    }
}
