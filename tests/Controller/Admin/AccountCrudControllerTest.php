<?php

namespace YunpianSmsBundle\Tests\Controller\Admin;

use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Controller\Admin\AccountCrudController;
use YunpianSmsBundle\Entity\Account;

class AccountCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn(): void
    {
        $this->assertEquals(Account::class, AccountCrudController::getEntityFqcn());
    }

    public function testControllerCanBeInstantiated(): void
    {
        $controller = new AccountCrudController();
        $this->assertInstanceOf(AccountCrudController::class, $controller);
    }
}
