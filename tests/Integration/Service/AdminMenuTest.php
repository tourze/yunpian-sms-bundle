<?php

namespace YunpianSmsBundle\Tests\Integration\Service;

use PHPUnit\Framework\TestCase;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use YunpianSmsBundle\Service\AdminMenu;

class AdminMenuTest extends TestCase
{
    public function testServiceCanBeInstantiated(): void
    {
        $linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        $service = new AdminMenu($linkGenerator);
        $this->assertInstanceOf(AdminMenu::class, $service);
    }
}