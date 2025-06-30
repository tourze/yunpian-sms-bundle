<?php

namespace YunpianSmsBundle\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\SendLog;

class SendLogTest extends TestCase
{
    public function testEntityCanBeInstantiated(): void
    {
        $entity = new SendLog();
        $this->assertInstanceOf(SendLog::class, $entity);
    }

    public function testEntityWithAccount(): void
    {
        $account = new Account();
        $entity = new SendLog();
        $entity->setAccount($account);

        $this->assertSame($account, $entity->getAccount());
    }

    public function testEntityWithMobile(): void
    {
        $mobile = '13800138000';
        $entity = new SendLog();
        $entity->setMobile($mobile);

        $this->assertEquals($mobile, $entity->getMobile());
    }
}