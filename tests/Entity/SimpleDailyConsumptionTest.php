<?php

namespace YunpianSmsBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use YunpianSmsBundle\Entity\DailyConsumption;
use YunpianSmsBundle\Tests\Mock\MockHelper;

/**
 * @internal
 */
#[CoversClass(DailyConsumption::class)]
final class SimpleDailyConsumptionTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new DailyConsumption();
    }

    public function testEntity(): void
    {
        $entity = new DailyConsumption();
        $this->assertInstanceOf(DailyConsumption::class, $entity);
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'totalCount' => ['totalCount', 100],
            'totalFee' => ['totalFee', '50.00'],
        ];
    }
}
