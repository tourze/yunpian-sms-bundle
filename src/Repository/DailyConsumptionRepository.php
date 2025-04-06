<?php

namespace YunpianSmsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\DailyConsumption;

/**
 * @extends ServiceEntityRepository<DailyConsumption>
 */
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
}
