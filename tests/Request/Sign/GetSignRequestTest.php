<?php

namespace YunpianSmsBundle\Tests\Request\Sign;

use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Request\Sign\GetSignRequest;

class GetSignRequestTest extends TestCase
{
    public function testGetRequestPath(): void
    {
        $request = new GetSignRequest();
        $this->assertEquals('/v2/sign/get.json', $request->getRequestPath());
    }

    public function testGetRequestMethod(): void
    {
        $request = new GetSignRequest();
        $this->assertEquals('POST', $request->getRequestMethod());
    }

    public function testGetRequestOptions(): void
    {
        $request = new GetSignRequest();
        
        $account = new Account();
        $account->setApiKey('test-api-key');
        $request->setAccount($account);
        
        $options = $request->getRequestOptions();
        $this->assertArrayHasKey('form_params', $options);
        $this->assertEquals('test-api-key', $options['form_params']['apikey']);
    }
    
    public function testGetRequestOptionsWithSignId(): void
    {
        $request = new GetSignRequest();
        
        $account = new Account();
        $account->setApiKey('test-api-key');
        $request->setAccount($account);
        
        $signId = 1001;
        $request->setSignId($signId);
        
        $options = $request->getRequestOptions();
        $this->assertArrayHasKey('form_params', $options);
        $this->assertEquals('test-api-key', $options['form_params']['apikey']);
        $this->assertEquals('1001', $options['form_params']['sign_id']);
    }
} 