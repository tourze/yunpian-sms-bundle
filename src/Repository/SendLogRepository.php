<?php

namespace YunpianSmsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use YunpianSmsBundle\Entity\SendLog;

/**
 * @extends ServiceEntityRepository<SendLog>
 */
class SendLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SendLog::class);
    }

    /**
     * @return SendLog[]
     */
    public function findPendingStatus(int $limit = 100): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.status IS NULL')
            ->andWhere('s.sid IS NOT NULL')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findOneBySid(string $sid): ?SendLog
    {
        return $this->findOneBy(['sid' => $sid]);
    }

    /**
     * 获取最后一条发送记录的时间
     */
    public function findLastSendTime(): ?\DateTimeInterface
    {
        $result = $this->createQueryBuilder('s')
            ->orderBy('s.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
            
        return $result?->getCreatedAt();
    }
}
