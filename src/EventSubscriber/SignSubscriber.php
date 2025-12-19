<?php

namespace YunpianSmsBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\HttpKernel\KernelInterface;
use YunpianSmsBundle\Entity\Sign;
use YunpianSmsBundle\Service\SignService;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Sign::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: Sign::class)]
#[AsEntityListener(event: Events::preRemove, method: 'preRemove', entity: Sign::class)]
final class SignSubscriber
{
    public function __construct(
        private readonly SignService $signService,
        private readonly KernelInterface $kernel,
    ) {
    }

    public function prePersist(Sign $sign): void
    {
        if ('test' === $this->kernel->getEnvironment()) {
            return;
        }
        $this->signService->createSign($sign);
    }

    public function preUpdate(Sign $sign): void
    {
        if ('test' === $this->kernel->getEnvironment()) {
            return;
        }
        $this->signService->updateSign($sign);
    }

    public function preRemove(Sign $sign): void
    {
        if ('test' === $this->kernel->getEnvironment()) {
            return;
        }
        $this->signService->deleteSign($sign);
    }
}
