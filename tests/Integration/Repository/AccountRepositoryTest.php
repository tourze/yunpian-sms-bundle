<?php

namespace YunpianSmsBundle\Tests\Integration\Repository;

use PHPUnit\Framework\TestCase;

class AccountRepositoryTest extends TestCase
{
    public function testRepositoryClassExists(): void
    {
        $this->assertTrue(class_exists(\YunpianSmsBundle\Repository\AccountRepository::class));
    }
}