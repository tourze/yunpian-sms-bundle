<?php

namespace YunpianSmsBundle\Tests\Request\SMS;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Request\SMS\GetDailyConsumptionRequest;

/**
 * @internal
 */
#[CoversClass(GetDailyConsumptionRequest::class)]
final class GetDailyConsumptionRequestTest extends TestCase
{
    public function testRequestCanBeInstantiated(): void
    {
        $request = new GetDailyConsumptionRequest();
        $this->assertInstanceOf(GetDailyConsumptionRequest::class, $request);
    }
}
