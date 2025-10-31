<?php

namespace YunpianSmsBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\SendLog;

/**
 * @internal
 */
#[CoversClass(SendLog::class)]
final class SendLogTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new SendLog();
    }

    public function testEntity(): void
    {
        $entity = new SendLog();
        $this->assertInstanceOf(SendLog::class, $entity);
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'mobile' => ['mobile', '13800138000'],
        ];
    }
}
