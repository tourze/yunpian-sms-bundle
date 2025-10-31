<?php

namespace YunpianSmsBundle\Tests\Request\Template;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Request\Template\GetTemplateListRequest;

/**
 * @internal
 */
#[CoversClass(GetTemplateListRequest::class)]
final class GetTemplateListRequestTest extends TestCase
{
    public function testGetRequestPath(): void
    {
        $request = new GetTemplateListRequest();
        $this->assertEquals('https://sms.yunpian.com/v2/tpl/del.json', $request->getRequestPath());
    }

    public function testGetRequestMethod(): void
    {
        $request = new GetTemplateListRequest();
        $this->assertEquals('POST', $request->getRequestMethod());
    }
}
