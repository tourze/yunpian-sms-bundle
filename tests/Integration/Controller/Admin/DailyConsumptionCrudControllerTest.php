<?php

namespace YunpianSmsBundle\Tests\Integration\Controller\Admin;

use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Controller\Admin\DailyConsumptionCrudController;
use YunpianSmsBundle\Entity\DailyConsumption;

class DailyConsumptionCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn(): void
    {
        $this->assertEquals(DailyConsumption::class, DailyConsumptionCrudController::getEntityFqcn());
    }

    public function testControllerCanBeInstantiated(): void
    {
        $controller = new DailyConsumptionCrudController();
        $this->assertInstanceOf(DailyConsumptionCrudController::class, $controller);
    }
}