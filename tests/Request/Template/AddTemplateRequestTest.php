<?php

namespace YunpianSmsBundle\Tests\Request\Template;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Request\Template\AddTemplateRequest;

/**
 * @internal
 */
#[CoversClass(AddTemplateRequest::class)]
final class AddTemplateRequestTest extends TestCase
{
    public function testGetRequestPath(): void
    {
        $request = new AddTemplateRequest();
        $this->assertEquals('https://sms.yunpian.com/v2/tpl/add.json', $request->getRequestPath());
    }

    public function testGetRequestMethod(): void
    {
        $request = new AddTemplateRequest();
        $this->assertEquals('POST', $request->getRequestMethod());
    }

    public function testGetRequestOptions(): void
    {
        $account = new Account();
        $account->setApiKey('test-api-key');

        $request = new AddTemplateRequest();
        $request->setAccount($account);
        $request->setContent('Your verification code is #code#');
        $request->setWebsite('https://example.com'); // 设置必需的网站属性

        $options = $request->getRequestOptions();

        $this->assertNotNull($options);
        $this->assertIsArray($options);
        $this->assertArrayHasKey('body', $options);

        $body = $options['body'];
        $this->assertIsString($body);

        $this->assertStringContainsString('apikey=test-api-key', $body);
        $this->assertStringContainsString('tpl_content=Your+verification+code+is+%23code%23', $body);
        $this->assertStringContainsString('website=https%3A%2F%2Fexample.com', $body);
    }
}
