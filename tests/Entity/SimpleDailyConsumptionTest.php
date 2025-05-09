<?php

namespace YunpianSmsBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Entity\DailyConsumption;
use YunpianSmsBundle\Tests\Mock\MockHelper;

class SimpleDailyConsumptionTest extends TestCase
{
    public function testBasicProperties(): void
    {
        $dailyConsumption = new DailyConsumption();
        
        $account = MockHelper::createAccount();
        $date = new \DateTime('2023-05-01');
        $totalCount = 100;
        $totalFee = '50.00';
        
        $dailyConsumption->setAccount($account);
        $dailyConsumption->setDate($date);
        $dailyConsumption->setTotalCount($totalCount);
        $dailyConsumption->setTotalFee($totalFee);
        
        $this->assertSame($account, $dailyConsumption->getAccount());
        $this->assertEquals($date, $dailyConsumption->getDate());
        $this->assertEquals($totalCount, $dailyConsumption->getTotalCount());
        $this->assertEquals($totalFee, $dailyConsumption->getTotalFee());
    }
} 