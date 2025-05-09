<?php

namespace YunpianSmsBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Request\SendTplSmsRequest;

class SendTplSmsRequestTest extends TestCase
{
    private SendTplSmsRequest $request;
    private Account $account;

    protected function setUp(): void
    {
        $this->request = new SendTplSmsRequest();
        
        $this->account = new Account();
        $this->account->setApiKey('test-api-key');
        $this->request->setAccount($this->account);
    }

    public function testGetRequestPath(): void
    {
        $this->assertEquals('/v2/sms/tpl_single_send.json', $this->request->getRequestPath());
    }

    public function testGetRequestMethod(): void
    {
        $this->assertEquals('POST', $this->request->getRequestMethod());
    }

    public function testGetRequestOptions_withRequiredFields(): void
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
        $options = $this->request->getRequestOptions();
        
        // 断言结果
        $this->assertIsArray($options);
        $this->assertArrayHasKey('form_params', $options);
        $this->assertArrayHasKey('apikey', $options['form_params']);
        $this->assertArrayHasKey('mobile', $options['form_params']);
        $this->assertArrayHasKey('tpl_id', $options['form_params']);
        $this->assertArrayHasKey('tpl_value', $options['form_params']);
        $this->assertEquals('test-api-key', $options['form_params']['apikey']);
        $this->assertEquals('13800138000', $options['form_params']['mobile']);
        $this->assertEquals('template-001', $options['form_params']['tpl_id']);
    }

    public function testGetRequestOptions_withAllFields(): void
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
        $options = $this->request->getRequestOptions();
        
        // 断言结果
        $this->assertIsArray($options);
        $this->assertArrayHasKey('form_params', $options);
        $this->assertArrayHasKey('apikey', $options['form_params']);
        $this->assertArrayHasKey('mobile', $options['form_params']);
        $this->assertArrayHasKey('tpl_id', $options['form_params']);
        $this->assertArrayHasKey('tpl_value', $options['form_params']);
        $this->assertArrayHasKey('uid', $options['form_params']);
        $this->assertEquals('test-api-key', $options['form_params']['apikey']);
        $this->assertEquals('13800138000', $options['form_params']['mobile']);
        $this->assertEquals('template-001', $options['form_params']['tpl_id']);
        $this->assertEquals('test-uid', $options['form_params']['uid']);
    }

    public function testGetRequestOptions_withMultipleMobiles(): void
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
        $options = $this->request->getRequestOptions();
        
        // 断言结果
        $this->assertIsArray($options);
        $this->assertArrayHasKey('form_params', $options);
        $this->assertArrayHasKey('mobile', $options['form_params']);
        $this->assertEquals('13800138000,13900139000', $options['form_params']['mobile']);
    }
} 