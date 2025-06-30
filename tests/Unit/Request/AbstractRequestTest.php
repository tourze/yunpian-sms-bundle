<?php

namespace YunpianSmsBundle\Tests\Unit\Request;

use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Request\AbstractRequest;

class AbstractRequestTest extends TestCase
{
    public function testAbstractRequestCannotBeInstantiatedDirectly(): void
    {
        $this->assertTrue(
            (new \ReflectionClass(AbstractRequest::class))->isAbstract(),
            'AbstractRequest should be an abstract class'
        );
    }
}