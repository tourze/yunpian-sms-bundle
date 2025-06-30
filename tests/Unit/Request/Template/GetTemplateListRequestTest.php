<?php

namespace YunpianSmsBundle\Tests\Unit\Request\Template;

use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Request\Template\GetTemplateListRequest;

class GetTemplateListRequestTest extends TestCase
{
    public function testRequestCanBeInstantiated(): void
    {
        $request = new GetTemplateListRequest();
        $this->assertInstanceOf(GetTemplateListRequest::class, $request);
    }
}