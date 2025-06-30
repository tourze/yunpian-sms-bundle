<?php

namespace YunpianSmsBundle\Tests\Unit\Exception;

use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Exception\TemplateParseException;

class TemplateParseExceptionTest extends TestCase
{
    public function testExceptionCanBeInstantiated(): void
    {
        $exception = new TemplateParseException('Test message');
        $this->assertInstanceOf(TemplateParseException::class, $exception);
        $this->assertEquals('Test message', $exception->getMessage());
    }

    public function testExceptionInheritsFromRuntimeException(): void
    {
        $exception = new TemplateParseException();
        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }
}