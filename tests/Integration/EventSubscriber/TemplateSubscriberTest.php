<?php

namespace YunpianSmsBundle\Tests\Integration\EventSubscriber;

use PHPUnit\Framework\TestCase;
use YunpianSmsBundle\EventSubscriber\TemplateSubscriber;
use YunpianSmsBundle\Service\TemplateService;

class TemplateSubscriberTest extends TestCase
{
    public function testSubscriberCanBeInstantiated(): void
    {
        $templateService = $this->createMock(TemplateService::class);
        $subscriber = new TemplateSubscriber($templateService);
        $this->assertInstanceOf(TemplateSubscriber::class, $subscriber);
    }
}