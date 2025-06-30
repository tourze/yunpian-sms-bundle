<?php

namespace YunpianSmsBundle\Tests\Unit\Request\Template;

use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Request\Template\UpdateTemplateRequest;

class UpdateTemplateRequestTest extends TestCase
{
    public function testRequestCanBeInstantiated(): void
    {
        $request = new UpdateTemplateRequest();
        $this->assertInstanceOf(UpdateTemplateRequest::class, $request);
    }
}