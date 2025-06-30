<?php

namespace YunpianSmsBundle\Tests\Integration\Repository;

use PHPUnit\Framework\TestCase;

class DailyConsumptionRepositoryTest extends TestCase
{
    public function testRepositoryClassExists(): void
    {
        $this->assertTrue(class_exists(\YunpianSmsBundle\Repository\DailyConsumptionRepository::class));
    }
}