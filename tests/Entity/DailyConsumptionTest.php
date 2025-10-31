<?php

namespace YunpianSmsBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\DailyConsumption;

/**
 * @internal
 */
#[CoversClass(DailyConsumption::class)]
final class DailyConsumptionTest extends AbstractEntityTestCase
{
    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'totalCount' => ['totalCount', 100];
        yield 'totalFee' => ['totalFee', '12.345'];
        yield 'totalSuccessCount' => ['totalSuccessCount', 80];
        yield 'totalFailedCount' => ['totalFailedCount', 15];
        yield 'totalUnknownCount' => ['totalUnknownCount', 5];
        yield 'items' => ['items', ['item1' => 'value1', 'item2' => 'value2']];
    }

    protected function createEntity(): DailyConsumption
    {
        return new DailyConsumption();
    }

    public function testIdGetter(): void
    {
        $dailyConsumption = new DailyConsumption();
        $this->assertEquals(0, $dailyConsumption->getId());
    }

    public function testDefaultValuesAreSetCorrectly(): void
    {
        $dailyConsumption = new DailyConsumption();
        $this->assertNull($dailyConsumption->getCreateTime());
        $this->assertNull($dailyConsumption->getUpdateTime());
        $this->assertEquals(0, $dailyConsumption->getId());
    }

    public function testEntityCanBeInstantiated(): void
    {
        $entity = new DailyConsumption();
        $this->assertInstanceOf(DailyConsumption::class, $entity);
    }

    public function testEntityWithAccount(): void
    {
        $account = new Account();
        $entity = new DailyConsumption();
        $entity->setAccount($account);

        $this->assertSame($account, $entity->getAccount());
    }

    public function testEntityWithDate(): void
    {
        $date = new \DateTimeImmutable();
        $entity = new DailyConsumption();
        $entity->setDate($date);

        $this->assertSame($date, $entity->getDate());
    }
}
