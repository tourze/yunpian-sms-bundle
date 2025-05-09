<?php

namespace YunpianSmsBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Entity\Account;

class AccountTest extends TestCase
{
    public function testConstructor(): void
    {
        $account = new Account();
        
        // 验证默认值
        $this->assertEquals(0, $account->getId());
        $this->assertFalse($account->isValid());
        $this->assertNull($account->getRemark());
    }
    
    public function testSetAndGetApiKey(): void
    {
        $account = new Account();
        
        $apiKey = 'test-api-key-123';
        $account->setApiKey($apiKey);
        
        $this->assertSame($apiKey, $account->getApiKey());
    }
    
    public function testSetAndGetValid(): void
    {
        $account = new Account();
        $this->assertFalse($account->isValid());
        
        $account->setValid(true);
        $this->assertTrue($account->isValid());
        
        $account->setValid(false);
        $this->assertFalse($account->isValid());
    }
    
    public function testSetAndGetRemark(): void
    {
        $account = new Account();
        $this->assertNull($account->getRemark());
        
        $remark = '这是一个测试账号';
        $account->setRemark($remark);
        
        $this->assertSame($remark, $account->getRemark());
        
        // 测试空字符串
        $account->setRemark('');
        $this->assertSame('', $account->getRemark());
        
        // 测试重置为null
        $account->setRemark(null);
        $this->assertNull($account->getRemark());
    }
    
    public function testSetAndGetCreateTime(): void
    {
        $account = new Account();
        
        $now = new \DateTime();
        $account->setCreateTime($now);
        
        $this->assertSame($now, $account->getCreateTime());
    }
    
    public function testSetAndGetUpdateTime(): void
    {
        $account = new Account();
        
        $now = new \DateTime();
        $account->setUpdateTime($now);
        
        $this->assertSame($now, $account->getUpdateTime());
    }
} 