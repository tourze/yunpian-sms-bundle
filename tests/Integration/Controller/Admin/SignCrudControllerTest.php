<?php

namespace YunpianSmsBundle\Tests\Integration\Controller\Admin;

use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Controller\Admin\SignCrudController;
use YunpianSmsBundle\Entity\Sign;

class SignCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn(): void
    {
        $this->assertEquals(Sign::class, SignCrudController::getEntityFqcn());
    }

    public function testControllerCanBeInstantiated(): void
    {
        $controller = new SignCrudController();
        $this->assertInstanceOf(SignCrudController::class, $controller);
    }
}