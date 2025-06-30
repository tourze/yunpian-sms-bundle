<?php

namespace YunpianSmsBundle\Tests\Unit\Request\Template;

use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Request\Template\AddTemplateRequest;

class AddTemplateRequestTest extends TestCase
{
    public function testRequestCanBeInstantiated(): void
    {
        $request = new AddTemplateRequest();
        $this->assertInstanceOf(AddTemplateRequest::class, $request);
    }
}