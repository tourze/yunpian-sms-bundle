<?php

namespace YunpianSmsBundle\Tests\Integration\Service;

use PHPUnit\Framework\TestCase;

class NotifierSmsTransportTest extends TestCase
{
    public function testTransportClassExists(): void
    {
        $this->assertTrue(class_exists(\YunpianSmsBundle\Service\NotifierSmsTransport::class));
    }
}