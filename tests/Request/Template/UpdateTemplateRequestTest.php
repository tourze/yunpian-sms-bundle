<?php

namespace YunpianSmsBundle\Tests\Request\Template;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Request\Template\UpdateTemplateRequest;

/**
 * @internal
 */
#[CoversClass(UpdateTemplateRequest::class)]
final class UpdateTemplateRequestTest extends TestCase
{
    public function testGetRequestPath(): void
    {
        $request = new UpdateTemplateRequest();
        $this->assertEquals('https://sms.yunpian.com/v2/tpl/update.json', $request->getRequestPath());
    }

    public function testGetRequestMethod(): void
    {
        $request = new UpdateTemplateRequest();
        $this->assertEquals('POST', $request->getRequestMethod());
    }
}
