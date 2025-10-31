<?php

declare(strict_types=1);

namespace YunpianSmsBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use YunpianSmsBundle\Exception\TemplateParseException;

/**
 * @internal
 */
#[CoversClass(TemplateParseException::class)]
final class TemplateParseExceptionTest extends AbstractExceptionTestCase
{
    public function testExceptionCanBeInstantiated(): void
    {
        $exception = new TemplateParseException('Test message');

        $this->assertInstanceOf(TemplateParseException::class, $exception);
        $this->assertInstanceOf(\RuntimeException::class, $exception);
        $this->assertEquals('Test message', $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
    }

    public function testExceptionWithEmptyMessage(): void
    {
        $exception = new TemplateParseException();

        $this->assertInstanceOf(\RuntimeException::class, $exception);
        $this->assertEquals('', $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
    }
}
