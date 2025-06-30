<?php

namespace YunpianSmsBundle\Tests\Integration\Controller\Admin;

use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Controller\Admin\TemplateCrudController;
use YunpianSmsBundle\Entity\Template;

class TemplateCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn(): void
    {
        $this->assertEquals(Template::class, TemplateCrudController::getEntityFqcn());
    }

    public function testControllerCanBeInstantiated(): void
    {
        $controller = new TemplateCrudController();
        $this->assertInstanceOf(TemplateCrudController::class, $controller);
    }
}