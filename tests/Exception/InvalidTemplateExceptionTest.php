<?php

declare(strict_types=1);

namespace YunpianSmsBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use YunpianSmsBundle\Exception\InvalidTemplateException;

/**
 * @internal
 */
#[CoversClass(InvalidTemplateException::class)]
final class InvalidTemplateExceptionTest extends AbstractExceptionTestCase
{
    public function testExceptionIsThrowable(): void
    {
        $exception = new InvalidTemplateException('Test message');

        $this->assertInstanceOf(\InvalidArgumentException::class, $exception);
        $this->assertEquals('Test message', $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
    }
}
