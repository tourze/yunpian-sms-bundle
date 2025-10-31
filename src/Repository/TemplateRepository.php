<?php

namespace YunpianSmsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\Template;

/**
 * @extends ServiceEntityRepository<Template>
 */
#[AsRepository(entityClass: Template::class)]
class TemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Template::class);
    }

    /**
     * @return Template[]
     */
    public function findByAccount(Account $account): array
    {
        return $this->findBy(['account' => $account]);
    }

    public function findOneByAccountAndTplId(Account $account, string $tplId): ?Template
    {
        return $this->findOneBy(['account' => $account, 'tplId' => $tplId]);
    }

    public function save(Template $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Template $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
