<?php

namespace YunpianSmsBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\DailyConsumption;
use YunpianSmsBundle\Repository\DailyConsumptionRepository;
use YunpianSmsBundle\Service\DailyConsumptionService;

/**
 * @internal
 */
#[CoversClass(DailyConsumptionService::class)]
#[RunTestsInSeparateProcesses]
final class DailyConsumptionServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testSyncDailyConsumption(): void
    {
        $service = self::getService(DailyConsumptionService::class);
        $entityManager = self::getService(EntityManagerInterface::class);
        $repository = self::getService(DailyConsumptionRepository::class);

        $account = new Account();
        $account->setApiKey('test-key');
        $account->setRemark('Test Account');
        $entityManager->persist($account);
        $entityManager->flush();

        $date = new \DateTime('2024-01-01');

        $result = $service->syncConsumption($account, $date);

        $this->assertNull($result, 'Sync should return null when no API data is available');
    }

    public function testSyncConsumptionCreatesNewRecord(): void
    {
        $service = self::getService(DailyConsumptionService::class);
        $entityManager = self::getService(EntityManagerInterface::class);

        $account = new Account();
        $account->setApiKey('test-key-2');
        $account->setRemark('Test Account 2');
        $entityManager->persist($account);
        $entityManager->flush();

        $date = new \DateTime('2024-01-02');

        $result = $service->create($account, $date, 100, '1.50', ['item1' => 'value1']);

        $this->assertInstanceOf(DailyConsumption::class, $result);
        $this->assertSame($account, $result->getAccount());
        $this->assertEquals($date, $result->getDate());
        $this->assertSame(100, $result->getTotalCount());
        $this->assertSame('1.50', $result->getTotalFee());
    }

    public function testCreate(): void
    {
        $service = self::getService(DailyConsumptionService::class);
        $entityManager = self::getService(EntityManagerInterface::class);

        $account = new Account();
        $account->setApiKey('create-test-key');
        $account->setRemark('Create Test Account');
        $entityManager->persist($account);
        $entityManager->flush();

        $date = new \DateTime('2024-01-03');
        $items = ['test' => 'data', 'count' => 42];

        $result = $service->create($account, $date, 200, '2.75', $items);

        $this->assertInstanceOf(DailyConsumption::class, $result);
        $this->assertSame($account, $result->getAccount());
        $this->assertEquals($date, $result->getDate());
        $this->assertSame(200, $result->getTotalCount());
        $this->assertSame('2.75', $result->getTotalFee());
        $this->assertEquals($items, $result->getItems());
    }
}
