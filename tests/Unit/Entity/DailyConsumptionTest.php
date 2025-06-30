<?php

namespace YunpianSmsBundle\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\DailyConsumption;

class DailyConsumptionTest extends TestCase
{
    public function testEntityCanBeInstantiated(): void
    {
        $entity = new DailyConsumption();
        $this->assertInstanceOf(DailyConsumption::class, $entity);
    }

    public function testEntityWithAccount(): void
    {
        $account = new Account();
        $entity = new DailyConsumption();
        $entity->setAccount($account);

        $this->assertSame($account, $entity->getAccount());
    }

    public function testEntityWithDate(): void
    {
        $date = new \DateTimeImmutable();
        $entity = new DailyConsumption();
        $entity->setDate($date);

        $this->assertSame($date, $entity->getDate());
    }
}