<?php

namespace YunpianSmsBundle\Tests\Request\Template;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Request\Template\DeleteTemplateRequest;

/**
 * @internal
 */
#[CoversClass(DeleteTemplateRequest::class)]
final class DeleteTemplateRequestTest extends TestCase
{
    public function testGetRequestPath(): void
    {
        $request = new DeleteTemplateRequest();
        $this->assertEquals('https://sms.yunpian.com/v2/tpl/del.json', $request->getRequestPath());
    }

    public function testGetRequestMethod(): void
    {
        $request = new DeleteTemplateRequest();
        $this->assertEquals('POST', $request->getRequestMethod());
    }
}
