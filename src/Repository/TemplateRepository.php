<?php

namespace YunpianSmsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\Template;

/**
 * @extends ServiceEntityRepository<Template>
 */
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
}
