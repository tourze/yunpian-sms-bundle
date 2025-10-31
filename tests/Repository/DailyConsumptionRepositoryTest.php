<?php

namespace YunpianSmsBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\DailyConsumption;
use YunpianSmsBundle\Repository\DailyConsumptionRepository;

/**
 * @internal
 */
#[CoversClass(DailyConsumptionRepository::class)]
#[RunTestsInSeparateProcesses]
final class DailyConsumptionRepositoryTest extends AbstractRepositoryTestCase
{
    private DailyConsumptionRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(DailyConsumptionRepository::class);
    }

    public function testRepositoryServiceIsAccessible(): void
    {
        $this->assertInstanceOf(DailyConsumptionRepository::class, $this->repository);
    }

    public function testFindOneByAccountAndDate(): void
    {
        $account = new Account();
        $account->setApiKey('test-daily-consumption-key');
        $account->setValid(true);

        $date = new \DateTimeImmutable('2024-01-15');
        $dailyConsumption = new DailyConsumption();
        $dailyConsumption->setAccount($account);
        $dailyConsumption->setDate($date);
        $dailyConsumption->setTotalCount(100);
        $dailyConsumption->setTotalFee('5.500');
        $dailyConsumption->setTotalSuccessCount(95);
        $dailyConsumption->setTotalFailedCount(5);
        $dailyConsumption->setTotalUnknownCount(0);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($dailyConsumption);
        self::getEntityManager()->flush();

        $foundConsumption = $this->repository->findOneByAccountAndDate($account, $date);
        $this->assertNotNull($foundConsumption);
        $this->assertSame($account, $foundConsumption->getAccount());
        $this->assertEquals($date, $foundConsumption->getDate());
        $this->assertSame(100, $foundConsumption->getTotalCount());
        $this->assertSame('5.500', $foundConsumption->getTotalFee());

        $notFoundConsumption = $this->repository->findOneByAccountAndDate($account, new \DateTimeImmutable('2024-01-16'));
        $this->assertNull($notFoundConsumption);

        self::getEntityManager()->remove($dailyConsumption);
        self::getEntityManager()->remove($account);
        self::getEntityManager()->flush();
    }

    public function testFindByAccount(): void
    {
        $account = new Account();
        $account->setApiKey('test-find-by-account-key');
        $account->setValid(true);

        $otherAccount = new Account();
        $otherAccount->setApiKey('other-account-key');
        $otherAccount->setValid(true);

        $consumption1 = new DailyConsumption();
        $consumption1->setAccount($account);
        $consumption1->setDate(new \DateTimeImmutable('2024-01-15'));
        $consumption1->setTotalCount(50);
        $consumption1->setTotalFee('2.500');
        $consumption1->setTotalSuccessCount(50);
        $consumption1->setTotalFailedCount(0);
        $consumption1->setTotalUnknownCount(0);

        $consumption2 = new DailyConsumption();
        $consumption2->setAccount($account);
        $consumption2->setDate(new \DateTimeImmutable('2024-01-14'));
        $consumption2->setTotalCount(75);
        $consumption2->setTotalFee('3.750');
        $consumption2->setTotalSuccessCount(70);
        $consumption2->setTotalFailedCount(5);
        $consumption2->setTotalUnknownCount(0);

        $otherConsumption = new DailyConsumption();
        $otherConsumption->setAccount($otherAccount);
        $otherConsumption->setDate(new \DateTimeImmutable('2024-01-15'));
        $otherConsumption->setTotalCount(25);
        $otherConsumption->setTotalFee('1.250');
        $otherConsumption->setTotalSuccessCount(25);
        $otherConsumption->setTotalFailedCount(0);
        $otherConsumption->setTotalUnknownCount(0);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($otherAccount);
        self::getEntityManager()->persist($consumption1);
        self::getEntityManager()->persist($consumption2);
        self::getEntityManager()->persist($otherConsumption);
        self::getEntityManager()->flush();

        $consumptions = $this->repository->findByAccount($account);
        $this->assertIsArray($consumptions);
        $this->assertCount(2, $consumptions);

        foreach ($consumptions as $consumption) {
            $this->assertSame($account, $consumption->getAccount());
        }

        $this->assertEquals(new \DateTimeImmutable('2024-01-15'), $consumptions[0]->getDate());
        $this->assertEquals(new \DateTimeImmutable('2024-01-14'), $consumptions[1]->getDate());

        self::getEntityManager()->remove($consumption1);
        self::getEntityManager()->remove($consumption2);
        self::getEntityManager()->remove($otherConsumption);
        self::getEntityManager()->remove($account);
        self::getEntityManager()->remove($otherAccount);
        self::getEntityManager()->flush();
    }

    public function testSaveMethodShouldPersistEntity(): void
    {
        $account = new Account();
        $account->setApiKey('test-save-' . uniqid());
        $account->setValid(true);

        $consumption = new DailyConsumption();
        $consumption->setAccount($account);
        $consumption->setDate(new \DateTimeImmutable('2024-01-25'));
        $consumption->setTotalCount(456);
        $consumption->setTotalFee('22.800');
        $consumption->setTotalSuccessCount(450);
        $consumption->setTotalFailedCount(6);
        $consumption->setTotalUnknownCount(0);

        self::getEntityManager()->persist($account);
        $this->repository->save($consumption);

        $foundConsumption = $this->repository->find($consumption->getId());
        $this->assertNotNull($foundConsumption);
        $this->assertSame(456, $foundConsumption->getTotalCount());

        self::getEntityManager()->remove($consumption);
        self::getEntityManager()->remove($account);
        self::getEntityManager()->flush();
    }

    public function testRemoveMethodShouldDeleteEntity(): void
    {
        $account = new Account();
        $account->setApiKey('test-remove-' . uniqid());
        $account->setValid(true);

        $consumption = new DailyConsumption();
        $consumption->setAccount($account);
        $consumption->setDate(new \DateTimeImmutable('2024-01-26'));
        $consumption->setTotalCount(789);
        $consumption->setTotalFee('39.450');
        $consumption->setTotalSuccessCount(780);
        $consumption->setTotalFailedCount(9);
        $consumption->setTotalUnknownCount(0);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($consumption);
        self::getEntityManager()->flush();
        $consumptionId = $consumption->getId();

        $this->repository->remove($consumption);

        $foundConsumption = $this->repository->find($consumptionId);
        $this->assertNull($foundConsumption);

        self::getEntityManager()->remove($account);
        self::getEntityManager()->flush();
    }

    public function testFindOneByWithOrderingShouldUseOrderBy(): void
    {
        $account = new Account();
        $account->setApiKey('test-order-find-one-' . uniqid());
        $account->setValid(true);

        $consumption1 = new DailyConsumption();
        $consumption1->setAccount($account);
        $consumption1->setDate(new \DateTimeImmutable('2024-01-28'));
        $consumption1->setTotalCount(100);
        $consumption1->setTotalFee('5.000');
        $consumption1->setTotalSuccessCount(95);
        $consumption1->setTotalFailedCount(5);
        $consumption1->setTotalUnknownCount(0);

        $consumption2 = new DailyConsumption();
        $consumption2->setAccount($account);
        $consumption2->setDate(new \DateTimeImmutable('2024-01-27'));
        $consumption2->setTotalCount(200);
        $consumption2->setTotalFee('10.000');
        $consumption2->setTotalSuccessCount(190);
        $consumption2->setTotalFailedCount(10);
        $consumption2->setTotalUnknownCount(0);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($consumption1);
        self::getEntityManager()->persist($consumption2);
        self::getEntityManager()->flush();

        $foundConsumption = $this->repository->findOneBy(['account' => $account], ['date' => 'ASC']);
        $this->assertNotNull($foundConsumption);
        $this->assertEquals(new \DateTimeImmutable('2024-01-27'), $foundConsumption->getDate());

        self::getEntityManager()->remove($consumption1);
        self::getEntityManager()->remove($consumption2);
        self::getEntityManager()->remove($account);
        self::getEntityManager()->flush();
    }

    public function testFindByAssociationWithAccountShouldReturnRelatedEntities(): void
    {
        $account1 = new Account();
        $account1->setApiKey('test-association-1-' . uniqid());
        $account1->setValid(true);

        $account2 = new Account();
        $account2->setApiKey('test-association-2-' . uniqid());
        $account2->setValid(true);

        $consumption1 = new DailyConsumption();
        $consumption1->setAccount($account1);
        $consumption1->setDate(new \DateTimeImmutable('2024-01-29'));
        $consumption1->setTotalCount(100);
        $consumption1->setTotalFee('5.000');
        $consumption1->setTotalSuccessCount(95);
        $consumption1->setTotalFailedCount(5);
        $consumption1->setTotalUnknownCount(0);

        $consumption2 = new DailyConsumption();
        $consumption2->setAccount($account2);
        $consumption2->setDate(new \DateTimeImmutable('2024-01-29'));
        $consumption2->setTotalCount(200);
        $consumption2->setTotalFee('10.000');
        $consumption2->setTotalSuccessCount(190);
        $consumption2->setTotalFailedCount(10);
        $consumption2->setTotalUnknownCount(0);

        self::getEntityManager()->persist($account1);
        self::getEntityManager()->persist($account2);
        self::getEntityManager()->persist($consumption1);
        self::getEntityManager()->persist($consumption2);
        self::getEntityManager()->flush();

        $account1Consumptions = $this->repository->findBy(['account' => $account1]);
        $this->assertIsArray($account1Consumptions);
        $this->assertCount(1, $account1Consumptions);
        $this->assertSame($account1, $account1Consumptions[0]->getAccount());

        self::getEntityManager()->remove($consumption1);
        self::getEntityManager()->remove($consumption2);
        self::getEntityManager()->remove($account1);
        self::getEntityManager()->remove($account2);
        self::getEntityManager()->flush();
    }

    public function testCountByAssociationWithAccountShouldReturnCorrectCount(): void
    {
        $account = new Account();
        $account->setApiKey('test-count-association-' . uniqid());
        $account->setValid(true);

        $consumption1 = new DailyConsumption();
        $consumption1->setAccount($account);
        $consumption1->setDate(new \DateTimeImmutable('2024-01-30'));
        $consumption1->setTotalCount(100);
        $consumption1->setTotalFee('5.000');
        $consumption1->setTotalSuccessCount(95);
        $consumption1->setTotalFailedCount(5);
        $consumption1->setTotalUnknownCount(0);

        $consumption2 = new DailyConsumption();
        $consumption2->setAccount($account);
        $consumption2->setDate(new \DateTimeImmutable('2024-01-31'));
        $consumption2->setTotalCount(200);
        $consumption2->setTotalFee('10.000');
        $consumption2->setTotalSuccessCount(190);
        $consumption2->setTotalFailedCount(10);
        $consumption2->setTotalUnknownCount(0);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($consumption1);
        self::getEntityManager()->persist($consumption2);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['account' => $account]);
        $this->assertSame(2, $count);

        self::getEntityManager()->remove($consumption1);
        self::getEntityManager()->remove($consumption2);
        self::getEntityManager()->remove($account);
        self::getEntityManager()->flush();
    }

    public function testFindByNullItemsShouldReturnEntitiesWithNullItems(): void
    {
        $account = new Account();
        $account->setApiKey('test-null-items-' . uniqid());
        $account->setValid(true);

        $consumption = new DailyConsumption();
        $consumption->setAccount($account);
        $consumption->setDate(new \DateTimeImmutable('2024-02-01'));
        $consumption->setTotalCount(100);
        $consumption->setTotalFee('5.000');
        $consumption->setTotalSuccessCount(95);
        $consumption->setTotalFailedCount(5);
        $consumption->setTotalUnknownCount(0);
        $consumption->setItems(null);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($consumption);
        self::getEntityManager()->flush();

        $nullItemsConsumptions = $this->repository->findBy(['items' => null]);
        $this->assertIsArray($nullItemsConsumptions);
        $this->assertGreaterThanOrEqual(1, count($nullItemsConsumptions));

        $found = false;
        foreach ($nullItemsConsumptions as $nullConsumption) {
            if ($nullConsumption->getId() === $consumption->getId()) {
                $found = true;
                $this->assertNull($nullConsumption->getItems());
            }
        }
        $this->assertTrue($found);

        self::getEntityManager()->remove($consumption);
        self::getEntityManager()->remove($account);
        self::getEntityManager()->flush();
    }

    public function testCountNullItemsFieldsShouldReturnCorrectCount(): void
    {
        $account1 = new Account();
        $account1->setApiKey('test-count-null-items-1-' . uniqid());
        $account1->setValid(true);

        $account2 = new Account();
        $account2->setApiKey('test-count-null-items-2-' . uniqid());
        $account2->setValid(true);

        $consumption1 = new DailyConsumption();
        $consumption1->setAccount($account1);
        $consumption1->setDate(new \DateTimeImmutable('2024-02-02'));
        $consumption1->setTotalCount(100);
        $consumption1->setTotalFee('5.000');
        $consumption1->setTotalSuccessCount(95);
        $consumption1->setTotalFailedCount(5);
        $consumption1->setTotalUnknownCount(0);
        $consumption1->setItems(null);

        $consumption2 = new DailyConsumption();
        $consumption2->setAccount($account2);
        $consumption2->setDate(new \DateTimeImmutable('2024-02-03'));
        $consumption2->setTotalCount(200);
        $consumption2->setTotalFee('10.000');
        $consumption2->setTotalSuccessCount(190);
        $consumption2->setTotalFailedCount(10);
        $consumption2->setTotalUnknownCount(0);
        $consumption2->setItems(null);

        self::getEntityManager()->persist($account1);
        self::getEntityManager()->persist($account2);
        self::getEntityManager()->persist($consumption1);
        self::getEntityManager()->persist($consumption2);
        self::getEntityManager()->flush();

        $nullItemsCount = $this->repository->count(['items' => null]);
        $this->assertGreaterThanOrEqual(2, $nullItemsCount);

        self::getEntityManager()->remove($consumption1);
        self::getEntityManager()->remove($consumption2);
        self::getEntityManager()->remove($account1);
        self::getEntityManager()->remove($account2);
        self::getEntityManager()->flush();
    }

    protected function createNewEntity(): object
    {
        $account = new Account();
        $account->setApiKey('test_api_key_' . uniqid());
        $account->setValid(true);
        $account->setRemark('Test Account for DailyConsumption');

        $entity = new DailyConsumption();
        $entity->setDate(new \DateTime('2024-01-01'));
        $entity->setAccount($account);
        $entity->setTotalCount(10);
        $entity->setTotalFee('0.5');

        return $entity;
    }

    protected function getRepository(): DailyConsumptionRepository
    {
        return $this->repository;
    }
}
