<?php

namespace YunpianSmsBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Request\SendSmsRequest;

class SendSmsRequestTest extends TestCase
{
    private SendSmsRequest $request;
    private Account $account;

    protected function setUp(): void
    {
        $this->request = new SendSmsRequest();
        
        $this->account = new Account();
        $this->account->setApiKey('test-api-key');
        $this->request->setAccount($this->account);
    }

    public function testGetRequestPath(): void
    {
        $this->assertEquals('/v2/sms/single_send.json', $this->request->getRequestPath());
    }

    public function testGetRequestMethod(): void
    {
        $this->assertEquals('POST', $this->request->getRequestMethod());
    }

    public function testGetRequestOptions_withRequiredFields(): void
    {
        // 准备测试数据
        $mobile = '13800138000';
        $content = '您的验证码是1234';
        
        // 设置请求参数
        $this->request->setMobile($mobile);
        $this->request->setContent($content);
        
        // 获取请求选项
        $options = $this->request->getRequestOptions();
        
        // 断言结果
        $this->assertArrayHasKey('form_params', $options);
        $this->assertArrayHasKey('apikey', $options['form_params']);
        $this->assertArrayHasKey('mobile', $options['form_params']);
        $this->assertArrayHasKey('text', $options['form_params']);
        $this->assertEquals('test-api-key', $options['form_params']['apikey']);
        $this->assertEquals('13800138000', $options['form_params']['mobile']);
        $this->assertEquals('您的验证码是1234', $options['form_params']['text']);
    }

    public function testGetRequestOptions_withUid(): void
    {
        // 准备测试数据
        $mobile = '13800138000';
        $content = '您的验证码是1234';
        $uid = 'test-uid';
        
        // 设置请求参数
        $this->request->setMobile($mobile);
        $this->request->setContent($content);
        $this->request->setUid($uid);
        
        // 获取请求选项
        $options = $this->request->getRequestOptions();
        
        // 断言结果
        $this->assertArrayHasKey('form_params', $options);
        $this->assertArrayHasKey('apikey', $options['form_params']);
        $this->assertArrayHasKey('mobile', $options['form_params']);
        $this->assertArrayHasKey('text', $options['form_params']);
        $this->assertArrayHasKey('uid', $options['form_params']);
        $this->assertEquals('test-api-key', $options['form_params']['apikey']);
        $this->assertEquals('13800138000', $options['form_params']['mobile']);
        $this->assertEquals('您的验证码是1234', $options['form_params']['text']);
        $this->assertEquals('test-uid', $options['form_params']['uid']);
    }

    public function testGetRequestOptions_withMultipleMobiles(): void
    {
        // 准备测试数据
        $mobile = '13800138000,13900139000';
        $content = '您的验证码是1234';
        
        // 设置请求参数
        $this->request->setMobile($mobile);
        $this->request->setContent($content);
        
        // 获取请求选项
        $options = $this->request->getRequestOptions();
        
        // 断言结果
        $this->assertArrayHasKey('form_params', $options);
        $this->assertArrayHasKey('mobile', $options['form_params']);
        $this->assertEquals('13800138000,13900139000', $options['form_params']['mobile']);
    }

    public function testGetRequestOptions_withLongContent(): void
    {
        // 准备测试数据
        $mobile = '13800138000';
        $content = str_repeat('测试内容', 100); // 创建一个长内容
        
        // 设置请求参数
        $this->request->setMobile($mobile);
        $this->request->setContent($content);
        
        // 获取请求选项
        $options = $this->request->getRequestOptions();
        
        // 断言结果
        $this->assertArrayHasKey('form_params', $options);
        $this->assertArrayHasKey('text', $options['form_params']);
        $this->assertEquals($content, $options['form_params']['text']);
    }
} 