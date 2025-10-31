<?php

namespace YunpianSmsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\DailyConsumption;

/**
 * @extends ServiceEntityRepository<DailyConsumption>
 */
#[AsRepository(entityClass: DailyConsumption::class)]
class DailyConsumptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DailyConsumption::class);
    }

    public function findOneByAccountAndDate(Account $account, \DateTimeInterface $date): ?DailyConsumption
    {
        return $this->findOneBy(['account' => $account, 'date' => $date]);
    }

    /**
     * @return DailyConsumption[]
     */
    public function findByAccount(Account $account): array
    {
        return $this->findBy(['account' => $account], ['date' => 'DESC']);
    }

    public function save(DailyConsumption $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DailyConsumption $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
