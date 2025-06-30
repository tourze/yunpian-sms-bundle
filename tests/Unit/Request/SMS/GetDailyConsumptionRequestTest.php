<?php

namespace YunpianSmsBundle\Tests\Unit\Request\SMS;

use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\Request\SMS\GetDailyConsumptionRequest;

class GetDailyConsumptionRequestTest extends TestCase
{
    public function testRequestCanBeInstantiated(): void
    {
        $request = new GetDailyConsumptionRequest();
        $this->assertInstanceOf(GetDailyConsumptionRequest::class, $request);
    }
}