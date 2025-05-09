<?php

namespace YunpianSmsBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Request\GetSendRecordRequest;

class GetSendRecordRequestTest extends TestCase
{
    public function testGetRequestPath(): void
    {
        $request = new GetSendRecordRequest();
        $this->assertEquals('/v2/sms/get_record.json', $request->getRequestPath());
    }

    public function testGetRequestMethod(): void
    {
        $request = new GetSendRecordRequest();
        $this->assertEquals('POST', $request->getRequestMethod());
    }

    public function testGetRequestOptions(): void
    {
        $request = new GetSendRecordRequest();
        
        $account = new Account();
        $account->setApiKey('test-api-key');
        $request->setAccount($account);
        
        $startTime = new \DateTime('2023-05-01');
        $endTime = new \DateTime('2023-05-02');
        $mobile = '13800138000';
        
        $request->setStartTime($startTime);
        $request->setEndTime($endTime);
        $request->setMobile($mobile);
        
        $options = $request->getRequestOptions();
        $this->assertArrayHasKey('form_params', $options);
        $this->assertEquals('test-api-key', $options['form_params']['apikey']);
        $this->assertEquals('2023-05-01 00:00:00', $options['form_params']['start_time']);
        $this->assertEquals('2023-05-02 00:00:00', $options['form_params']['end_time']);
        $this->assertEquals('13800138000', $options['form_params']['mobile']);
    }
    
    public function testGetters(): void
    {
        $request = new GetSendRecordRequest();
        
        $startTime = new \DateTime('2023-05-01');
        $endTime = new \DateTime('2023-05-02');
        $mobile = '13800138000';
        
        $request->setStartTime($startTime);
        $request->setEndTime($endTime);
        $request->setMobile($mobile);
        
        $this->assertSame($startTime, $request->getStartTime());
        $this->assertSame($endTime, $request->getEndTime());
        $this->assertEquals($mobile, $request->getMobile());
    }
} 