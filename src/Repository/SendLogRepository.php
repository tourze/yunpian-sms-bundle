<?php

namespace YunpianSmsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use YunpianSmsBundle\Entity\SendLog;
use YunpianSmsBundle\Enum\SendStatusEnum;

/**
 * @extends ServiceEntityRepository<SendLog>
 */
#[AsRepository(entityClass: SendLog::class)]
class SendLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SendLog::class);
    }

    /**
     * @return list<SendLog>
     */
    public function findPendingStatus(int $limit = 100): array
    {
        /** @var list<SendLog> */
        return $this->createQueryBuilder('s')
            ->where('s.status = :pending')
            ->andWhere('s.sid IS NOT NULL')
            ->setParameter('pending', SendStatusEnum::PENDING)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneBySid(string $sid): ?SendLog
    {
        return $this->findOneBy(['sid' => $sid]);
    }

    /**
     * 获取最后一条发送记录的时间
     */
    public function findLastSendTime(): ?\DateTimeImmutable
    {
        $qb = $this->createQueryBuilder('s')
            ->orderBy('s.createTime', 'DESC')
            ->setMaxResults(1)
        ;

        $result = $qb->getQuery()->getOneOrNullResult();

        if ($result instanceof SendLog) {
            return $result->getCreateTime();
        }

        return null;
    }

    public function save(SendLog $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SendLog $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
