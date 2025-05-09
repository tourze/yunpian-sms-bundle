<?php

namespace YunpianSmsBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Request\GetSendStatusRequest;

class GetSendStatusRequestTest extends TestCase
{
    public function testGetRequestPath(): void
    {
        $request = new GetSendStatusRequest();
        $this->assertEquals('/v2/sms/pull_status.json', $request->getRequestPath());
    }

    public function testGetRequestMethod(): void
    {
        $request = new GetSendStatusRequest();
        $this->assertEquals('POST', $request->getRequestMethod());
    }

    public function testGetRequestOptions(): void
    {
        $request = new GetSendStatusRequest();
        
        $account = new Account();
        $account->setApiKey('test-api-key');
        $request->setAccount($account);
        
        $sids = [1001, 1002, 1003];
        $request->setSids($sids);
        
        $options = $request->getRequestOptions();
        $this->assertArrayHasKey('form_params', $options);
        $this->assertEquals('test-api-key', $options['form_params']['apikey']);
        $this->assertEquals('1001,1002,1003', $options['form_params']['sid']);
    }
    
    public function testGetSids(): void
    {
        $request = new GetSendStatusRequest();
        
        $sids = [1001, 1002, 1003];
        $request->setSids($sids);
        
        $this->assertEquals($sids, $request->getSids());
    }
} 