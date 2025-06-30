<?php

namespace YunpianSmsBundle\Tests\Integration\Controller\Admin;

use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Controller\Admin\SendLogCrudController;
use YunpianSmsBundle\Entity\SendLog;

class SendLogCrudControllerTest extends TestCase
{
    public function testGetEntityFqcn(): void
    {
        $this->assertEquals(SendLog::class, SendLogCrudController::getEntityFqcn());
    }

    public function testControllerCanBeInstantiated(): void
    {
        $controller = new SendLogCrudController();
        $this->assertInstanceOf(SendLogCrudController::class, $controller);
    }
}