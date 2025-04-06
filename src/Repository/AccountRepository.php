<?php

namespace YunpianSmsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use YunpianSmsBundle\Entity\Account;

/**
 * @extends ServiceEntityRepository<Account>
 */
class AccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Account::class);
    }

    public function findOneByApiKey(string $apiKey): ?Account
    {
        return $this->findOneBy(['apiKey' => $apiKey]);
    }

    /**
     * @return Account[]
     */
    public function findAllValid(): array
    {
        return $this->findBy(['valid' => true]);
    }
}
