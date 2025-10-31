<?php

namespace YunpianSmsBundle\Tests\Request;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Request\AbstractRequest;

/**
 * @internal
 */
#[CoversClass(AbstractRequest::class)]
final class AbstractRequestTest extends TestCase
{
    public function testAbstractRequestCannotBeInstantiatedDirectly(): void
    {
        $this->assertTrue(
            (new \ReflectionClass(AbstractRequest::class))->isAbstract(),
            'AbstractRequest should be an abstract class'
        );
    }
}
