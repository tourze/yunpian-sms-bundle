<?php

namespace YunpianSmsBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\Sign;

class SimpleSignTest extends TestCase
{
    public function testBasicProperties(): void
    {
        $sign = new Sign();
        
        $account = new Account();
        $account->setApiKey('test-api-key');
        
        $sign->setAccount($account);
        $sign->setSign('测试签名');
        $sign->setApplyState('SUCCESS');
        $sign->setValid(true);
        
        $this->assertSame($account, $sign->getAccount());
        $this->assertEquals('测试签名', $sign->getSign());
        $this->assertEquals('SUCCESS', $sign->getApplyState());
        $this->assertTrue($sign->isValid());
    }
} 