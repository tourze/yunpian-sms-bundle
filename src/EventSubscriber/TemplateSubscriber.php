<?php

namespace YunpianSmsBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\HttpKernel\KernelInterface;
use YunpianSmsBundle\Entity\Template;
use YunpianSmsBundle\Service\TemplateService;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Template::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: Template::class)]
#[AsEntityListener(event: Events::preRemove, method: 'preRemove', entity: Template::class)]
final class TemplateSubscriber
{
    public function __construct(
        private readonly TemplateService $templateService,
        private readonly KernelInterface $kernel,
    ) {
    }

    public function prePersist(Template $template): void
    {
        if ('test' === $this->kernel->getEnvironment()) {
            return;
        }
        $this->templateService->createTemplate($template);
    }

    public function preUpdate(Template $template): void
    {
        if ('test' === $this->kernel->getEnvironment()) {
            return;
        }
        // Note: The update method in TemplateService expects two parameters
        // We'll use the existing content since we're in preUpdate
        $this->templateService->update($template, $template->getContent());
    }

    public function preRemove(Template $template): void
    {
        if ('test' === $this->kernel->getEnvironment()) {
            return;
        }
        $this->templateService->delete($template);
    }
}
