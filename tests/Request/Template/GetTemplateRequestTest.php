<?php

namespace YunpianSmsBundle\Tests\Request\Template;

use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Request\Template\GetTemplateRequest;

class GetTemplateRequestTest extends TestCase
{
    public function testGetRequestPath(): void
    {
        $request = new GetTemplateRequest();
        $this->assertEquals('/v2/tpl/get.json', $request->getRequestPath());
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
        $this->assertArrayHasKey('form_params', $options);
        $this->assertEquals('test-api-key', $options['form_params']['apikey']);
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
        $this->assertArrayHasKey('form_params', $options);
        $this->assertEquals('test-api-key', $options['form_params']['apikey']);
        $this->assertEquals('template-001', $options['form_params']['tpl_id']);
    }
} 