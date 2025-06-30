<?php

namespace YunpianSmsBundle\Tests\Unit\Request\Template;

use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Request\Template\DeleteTemplateRequest;

class DeleteTemplateRequestTest extends TestCase
{
    public function testRequestCanBeInstantiated(): void
    {
        $request = new DeleteTemplateRequest();
        $this->assertInstanceOf(DeleteTemplateRequest::class, $request);
    }
}