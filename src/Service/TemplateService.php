<?php

namespace YunpianSmsBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\Template;
use YunpianSmsBundle\Enum\NotifyTypeEnum;
use YunpianSmsBundle\Enum\TemplateTypeEnum;
use YunpianSmsBundle\Repository\TemplateRepository;
use YunpianSmsBundle\Request\Template\AddTemplateRequest;
use YunpianSmsBundle\Request\Template\DeleteTemplateRequest;
use YunpianSmsBundle\Request\Template\GetTemplateListRequest;
use YunpianSmsBundle\Request\Template\UpdateTemplateRequest;

class TemplateService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TemplateRepository $templateRepository,
        private readonly SmsApiClient $apiClient,
    ) {
    }

    public function syncTemplates(Account $account): void
    {
        // 获取远程模板列表
        $request = new GetTemplateListRequest();
        $request->setAccount($account);
        $response = $this->apiClient->request($request);

        foreach ($response as $tpl) {
            $template = $this->templateRepository->findOneByAccountAndTplId($account, $tpl['tpl_id']);

            if (!$template) {
                $template = new Template();
                $template->setAccount($account);
                $template->setTplId($tpl['tpl_id']);
                $template->setNotifyType(NotifyTypeEnum::ALWAYS);
                $template->setTemplateType(isset($tpl['tpl_type']) ? TemplateTypeEnum::from($tpl['tpl_type']) : TemplateTypeEnum::NOTIFICATION);
            }

            $template->setTitle($tpl['tpl_title'] ?? '');
            $template->setContent($tpl['tpl_content']);
            $template->setCheckStatus($tpl['check_status']);
            $template->setCheckReply($tpl['check_reply'] ?? null);
            $template->setWebsite($tpl['website'] ?? null);
            $template->setApplyDescription($tpl['apply_description'] ?? null);

            $this->entityManager->persist($template);
        }

        $this->entityManager->flush();
    }

    public function createTemplate(Template $template): void
    {
        if ($template->getTemplateType()->isVerification() && (empty($template->getWebsite()) || empty($template->getApplyDescription()))) {
            throw new \InvalidArgumentException('验证码类模板必须提供网站地址和说明');
        }

        $request = new AddTemplateRequest();
        $request->setAccount($template->getAccount());
        $request->setContent($template->getContent());
        $request->setNotifyType($template->getNotifyType());
        $request->setWebsite($template->getWebsite());
        $request->setTemplateType($template->getTemplateType());
        $request->setCallback($template->getCallback());
        $request->setApplyDescription($template->getApplyDescription());

        $response = $this->apiClient->request($request);
        $template->setTplId($response['tpl_id']);

        $this->entityManager->persist($template);
        $this->entityManager->flush();
    }

    public function updateTemplate(Template $template): void
    {
        if ($template->getTemplateType()->isVerification() && (empty($template->getWebsite()) || empty($template->getApplyDescription()))) {
            throw new \InvalidArgumentException('验证码类模板必须提供网站地址和说明');
        }

        $request = new UpdateTemplateRequest();
        $request->setAccount($template->getAccount());
        $request->setTplId($template->getTplId());
        $request->setContent($template->getContent());
        $request->setNotifyType($template->getNotifyType());
        $request->setWebsite($template->getWebsite());
        $request->setTemplateType($template->getTemplateType());
        $request->setCallback($template->getCallback());
        $request->setApplyDescription($template->getApplyDescription());

        $this->apiClient->request($request);
        $this->entityManager->flush();
    }

    public function deleteTemplate(Template $template): void
    {
        $request = new DeleteTemplateRequest();
        $request->setAccount($template->getAccount());
        $request->setTemplateId($template->getTplId());

        $this->apiClient->request($request);
        $this->entityManager->remove($template);
        $this->entityManager->flush();
    }

    /**
     * @return Template[]
     */
    public function findByAccount(Account $account): array
    {
        return $this->templateRepository->findByAccount($account);
    }

    public function findOneByAccountAndTplId(Account $account, string $tplId): ?Template
    {
        return $this->templateRepository->findOneByAccountAndTplId($account, $tplId);
    }
}
