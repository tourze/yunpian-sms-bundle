<?php

declare(strict_types=1);

namespace YunpianSmsBundle\Tests\Unit\Exception;

use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Exception\InvalidTemplateException;

class InvalidTemplateExceptionTest extends TestCase
{
    public function testExceptionIsThrowable(): void
    {
        $exception = new InvalidTemplateException('Test message');
        
        $this->assertInstanceOf(\InvalidArgumentException::class, $exception);
        $this->assertEquals('Test message', $exception->getMessage());
    }
}