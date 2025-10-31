<?php

namespace YunpianSmsBundle\Tests\Request\Template;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Request\Template\GetTemplateRequest;

/**
 * @internal
 */
#[CoversClass(GetTemplateRequest::class)]
final class GetTemplateRequestTest extends TestCase
{
    public function testGetRequestPath(): void
    {
        $request = new GetTemplateRequest();
        $this->assertEquals('https://sms.yunpian.com/v2/tpl/get.json', $request->getRequestPath());
    }

    public function testGetRequestMethod(): void
    {
        $request = new GetTemplateRequest();
        $this->assertEquals('POST', $request->getRequestMethod());
    }

    public function testGetRequestOptions(): void
    {
        $request = new GetTemplateRequest();

        $account = new Account();
        $account->setApiKey('test-api-key');
        $request->setAccount($account);

        $options = $request->getRequestOptions();
        $this->assertIsArray($options);
        $this->assertArrayHasKey('body', $options);

        $body = $options['body'];
        $this->assertIsString($body);

        $this->assertStringContainsString('apikey=test-api-key', $body);
    }

    public function testGetRequestOptionsWithTplId(): void
    {
        $request = new GetTemplateRequest();

        $account = new Account();
        $account->setApiKey('test-api-key');
        $request->setAccount($account);

        $tplId = 'template-001';
        $request->setTplId($tplId);

        $options = $request->getRequestOptions();
        $this->assertIsArray($options);
        $this->assertArrayHasKey('body', $options);

        $body = $options['body'];
        $this->assertIsString($body);

        $this->assertStringContainsString('apikey=test-api-key', $body);
        $this->assertStringContainsString('tpl_id=template-001', $body);
    }
}
