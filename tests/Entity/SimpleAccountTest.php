<?php

namespace YunpianSmsBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Entity\Account;

class SimpleAccountTest extends TestCase
{
    public function testGetId(): void
    {
        $account = new Account();
        $this->assertEquals(0, $account->getId());
    }

    public function testIsValid(): void
    {
        $account = new Account();
        $this->assertFalse($account->isValid());
        
        $account->setValid(true);
        $this->assertTrue($account->isValid());
    }
    
    public function testApiKey(): void
    {
        $account = new Account();
        $apiKey = 'test-api-key';
        $account->setApiKey($apiKey);
        $this->assertEquals($apiKey, $account->getApiKey());
    }
} 