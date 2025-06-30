<?php

namespace YunpianSmsBundle\Tests\Controller\Admin;

use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Controller\Admin\AccountCrudController;
use YunpianSmsBundle\Controller\Admin\DailyConsumptionCrudController;
use YunpianSmsBundle\Controller\Admin\SendLogCrudController;
use YunpianSmsBundle\Controller\Admin\SignCrudController;
use YunpianSmsBundle\Controller\Admin\TemplateCrudController;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\DailyConsumption;
use YunpianSmsBundle\Entity\SendLog;
use YunpianSmsBundle\Entity\Sign;
use YunpianSmsBundle\Entity\Template;

class AllCrudControllersTest extends TestCase
{
    public static function controllerProvider(): array
    {
        return [
            [AccountCrudController::class],
            [TemplateCrudController::class],
            [SignCrudController::class],
            [SendLogCrudController::class],
            [DailyConsumptionCrudController::class],
        ];
    }

    public static function controllerEntityProvider(): array
    {
        return [
            [AccountCrudController::class, Account::class],
            [TemplateCrudController::class, Template::class],
            [SignCrudController::class, Sign::class],
            [SendLogCrudController::class, SendLog::class],
            [DailyConsumptionCrudController::class, DailyConsumption::class],
        ];
    }

    /**
     * @dataProvider controllerProvider
     */
    public function testControllerCanBeInstantiated(string $controllerClass): void
    {
        $controller = new $controllerClass();
        $this->assertInstanceOf($controllerClass, $controller);
    }

    /**
     * @dataProvider controllerEntityProvider
     */
    public function testGetEntityFqcn(string $controllerClass, string $expectedEntityClass): void
    {
        $this->assertEquals($expectedEntityClass, $controllerClass::getEntityFqcn());
    }
}
