<?php

namespace YunpianSmsBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use YunpianSmsBundle\Entity\Template;
use YunpianSmsBundle\Service\TemplateService;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Template::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: Template::class)]
#[AsEntityListener(event: Events::preRemove, method: 'preRemove', entity: Template::class)]
class TemplateSubscriber
{
    public function __construct(
        private readonly TemplateService $templateService,
    ) {
    }

    public function prePersist(Template $template): void
    {
        $this->templateService->createTemplate($template);
    }

    public function preUpdate(Template $template): void
    {
        $this->templateService->updateTemplate($template);
    }

    public function preRemove(Template $template): void
    {
        $this->templateService->deleteTemplate($template);
    }
}
