<?php

namespace YunpianSmsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\Sign;

/**
 * @extends ServiceEntityRepository<Sign>
 */
class SignRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sign::class);
    }

    /**
     * @return Sign[]
     */
    public function findByAccount(Account $account): array
    {
        return $this->findBy(['account' => $account]);
    }

    public function findOneByAccountAndSign(Account $account, string $sign): ?Sign
    {
        return $this->findOneBy(['account' => $account, 'sign' => $sign]);
    }
}
