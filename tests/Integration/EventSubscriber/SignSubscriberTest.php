<?php

namespace YunpianSmsBundle\Tests\Integration\EventSubscriber;

use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\EventSubscriber\SignSubscriber;
use YunpianSmsBundle\Service\SignService;

class SignSubscriberTest extends TestCase
{
    public function testSubscriberCanBeInstantiated(): void
    {
        $signService = $this->createMock(SignService::class);
        $subscriber = new SignSubscriber($signService);
        $this->assertInstanceOf(SignSubscriber::class, $subscriber);
    }
}