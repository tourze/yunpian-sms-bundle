<?php

namespace YunpianSmsBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use YunpianSmsBundle\Entity\Sign;
use YunpianSmsBundle\Service\SignService;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Sign::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: Sign::class)]
#[AsEntityListener(event: Events::preRemove, method: 'preRemove', entity: Sign::class)]
class SignSubscriber
{
    public function __construct(
        private readonly SignService $signService,
    ) {
    }

    public function prePersist(Sign $sign): void
    {
        $this->signService->createSign($sign);
    }

    public function preUpdate(Sign $sign): void
    {
        $this->signService->updateSign($sign);
    }

    public function preRemove(Sign $sign): void
    {
        $this->signService->deleteSign($sign);
    }
}
