<?php

namespace YunpianSmsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use YunpianSmsBundle\Entity\Account;

/**
 * @extends ServiceEntityRepository<Account>
 */
#[AsRepository(entityClass: Account::class)]
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

    public function save(Account $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Account $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
