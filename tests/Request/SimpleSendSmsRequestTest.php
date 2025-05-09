<?php

namespace YunpianSmsBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Request\SendSmsRequest;

class SimpleSendSmsRequestTest extends TestCase
{
    public function testGetRequestPath(): void
    {
        $request = new SendSmsRequest();
        $this->assertEquals('/v2/sms/single_send.json', $request->getRequestPath());
    }

    public function testGetRequestMethod(): void
    {
        $request = new SendSmsRequest();
        $this->assertEquals('POST', $request->getRequestMethod());
    }
    
    public function testGetRequestOptions(): void
    {
        $request = new SendSmsRequest();
        
        $account = new Account();
        $account->setApiKey('test-api-key');
        $request->setAccount($account);
        
        $mobile = '13800138000';
        $request->setMobile($mobile);
        
        $content = '测试短信内容';
        $request->setContent($content);
        
        $options = $request->getRequestOptions();
        $this->assertArrayHasKey('form_params', $options);
        $this->assertEquals('test-api-key', $options['form_params']['apikey']);
        $this->assertEquals($mobile, $options['form_params']['mobile']);
        $this->assertEquals($content, $options['form_params']['text']);
    }
} 