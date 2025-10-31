<?php

namespace YunpianSmsBundle\Tests\Request;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Request\SendTplSmsRequest;

/**
 * @internal
 */
#[CoversClass(SendTplSmsRequest::class)]
final class SendTplSmsRequestTest extends TestCase
{
    private SendTplSmsRequest $request;

    private Account $account;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new SendTplSmsRequest();

        $this->account = new Account();
        $this->account->setApiKey('test-api-key');
        $this->request->setAccount($this->account);
    }

    public function testGetRequestPath(): void
    {
        $this->assertEquals('https://sms.yunpian.com/v2/sms/tpl_single_send.json', $this->request->getRequestPath());
    }

    public function testGetRequestMethod(): void
    {
        $this->assertEquals('POST', $this->request->getRequestMethod());
    }

    public function testGetRequestOptionsWithRequiredFields(): void
    {
        // 准备测试数据
        $mobile = '13800138000';
        $tplId = 'template-001';
        $tplValue = ['code' => '1234'];

        // 设置请求参数
        $this->request->setMobile($mobile);
        $this->request->setTplId($tplId);
        $this->request->setTplValue($tplValue);

        // 获取请求选项
        /** @var array<string, mixed> $options */
        $options = $this->request->getRequestOptions();

        // 断言结果
        $this->assertIsArray($options);
        $this->assertArrayHasKey('body', $options);

        /** @var string $body */
        $body = $options['body'];
        $this->assertStringContainsString('apikey=test-api-key', $body);
        $this->assertStringContainsString('mobile=13800138000', $body);
        $this->assertStringContainsString('tpl_id=template-001', $body);
    }

    public function testGetRequestOptionsWithAllFields(): void
    {
        // 准备测试数据
        $mobile = '13800138000';
        $tplId = 'template-001';
        $tplValue = ['code' => '1234', 'name' => '测试用户'];
        $uid = 'test-uid';

        // 设置请求参数
        $this->request->setMobile($mobile);
        $this->request->setTplId($tplId);
        $this->request->setTplValue($tplValue);
        $this->request->setUid($uid);

        // 获取请求选项
        /** @var array<string, mixed> $options */
        $options = $this->request->getRequestOptions();

        // 断言结果
        $this->assertIsArray($options);
        $this->assertArrayHasKey('body', $options);

        /** @var string $body */
        $body = $options['body'];
        $this->assertStringContainsString('apikey=test-api-key', $body);
        $this->assertStringContainsString('mobile=13800138000', $body);
        $this->assertStringContainsString('tpl_id=template-001', $body);
        $this->assertStringContainsString('uid=test-uid', $body);
    }

    public function testGetRequestOptionsWithMultipleMobiles(): void
    {
        // 准备测试数据
        $mobile = '13800138000,13900139000';
        $tplId = 'template-001';
        $tplValue = ['code' => '1234'];

        // 设置请求参数
        $this->request->setMobile($mobile);
        $this->request->setTplId($tplId);
        $this->request->setTplValue($tplValue);

        // 获取请求选项
        /** @var array<string, mixed> $options */
        $options = $this->request->getRequestOptions();

        // 断言结果
        $this->assertIsArray($options);
        $this->assertArrayHasKey('body', $options);

        /** @var string $body */
        $body = $options['body'];
        $this->assertStringContainsString('mobile=13800138000%2C13900139000', $body);
    }
}
