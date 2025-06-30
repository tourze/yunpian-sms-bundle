<?php

namespace YunpianSmsBundle\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\Sign;

class SignTest extends TestCase
{
    public function testEntityCanBeInstantiated(): void
    {
        $entity = new Sign();
        $this->assertInstanceOf(Sign::class, $entity);
    }

    public function testEntityWithAccount(): void
    {
        $account = new Account();
        $entity = new Sign();
        $entity->setAccount($account);

        $this->assertSame($account, $entity->getAccount());
    }

    public function testEntityWithSign(): void
    {
        $signText = 'Test Sign';
        $entity = new Sign();
        $entity->setSign($signText);

        $this->assertEquals($signText, $entity->getSign());
    }
}