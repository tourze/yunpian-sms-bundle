<?php

namespace YunpianSmsBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\Template;
use YunpianSmsBundle\Enum\NotifyTypeEnum;
use YunpianSmsBundle\Enum\TemplateTypeEnum;
use YunpianSmsBundle\Repository\TemplateRepository;
use YunpianSmsBundle\Request\Template\AddTemplateRequest;
use YunpianSmsBundle\Request\Template\DeleteTemplateRequest;
use YunpianSmsBundle\Request\Template\GetTemplateRequest;
use YunpianSmsBundle\Request\Template\UpdateTemplateRequest;

class TemplateService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TemplateRepository $templateRepository,
        private readonly SmsApiClient $apiClient,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * 同步模板
     * 
     * @return Template[]
     */
    public function syncTemplates(Account $account): array
    {
        try {
            // 获取远程模板列表
            $request = new GetTemplateRequest();
            $request->setAccount($account);
            $response = $this->apiClient->request($request);
            
            $result = [];
    
            foreach ($response as $tpl) {
                $template = $this->templateRepository->findOneBy([
                    'account' => $account,
                    'tplId' => $tpl['tpl_id'],
                ]);
    
                if (!$template) {
                    $template = new Template();
                    $template->setAccount($account);
                    $template->setTplId($tpl['tpl_id']);
                    $template->setNotifyType(NotifyTypeEnum::ALWAYS);
                    $template->setTemplateType(isset($tpl['tpl_type']) ? TemplateTypeEnum::from($tpl['tpl_type']) : TemplateTypeEnum::NOTIFICATION);
                    $this->entityManager->persist($template);
                }
    
                $template->setTitle($tpl['tpl_title'] ?? '');
                $template->setContent($tpl['tpl_content']);
                $template->setCheckStatus($tpl['check_status']);
                $template->setCheckReply($tpl['reason'] ?? null);
                
                $result[] = $template;
            }
    
            $this->entityManager->flush();
            return $result;
        } catch (\Exception $e) {
            $this->logger->error('同步模板失败: {message}', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);
            return [];
        }
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
    
    /**
     * 创建模板
     */
    public function create(Account $account, string $tplContent, bool $notify = true): Template
    {
        try {
            $request = new AddTemplateRequest();
            $request->setAccount($account);
            $request->setContent($tplContent);
            $request->setNotifyType($notify ? NotifyTypeEnum::ALWAYS : NotifyTypeEnum::ONLY_FAILED);
            
            $response = $this->apiClient->request($request);
            
            $template = new Template();
            $template->setAccount($account);
            $template->setTplId($response['tpl_id']);
            $template->setContent($tplContent);
            $template->setNotifyType($notify ? NotifyTypeEnum::ALWAYS : NotifyTypeEnum::ONLY_FAILED);
            $template->setTemplateType(TemplateTypeEnum::NOTIFICATION);
            $template->setCheckStatus('SUCCESS');
            
            $this->entityManager->persist($template);
            $this->entityManager->flush();
            
            return $template;
        } catch (\Exception $e) {
            $this->logger->error('创建模板失败: {message}', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * 更新模板
     */
    public function update(Template $template, string $newContent): Template
    {
        try {
            $request = new UpdateTemplateRequest();
            $request->setAccount($template->getAccount());
            $request->setTplId($template->getTplId());
            $request->setContent($newContent);
            
            $this->apiClient->request($request);
            
            $template->setContent($newContent);
            $this->entityManager->flush();
            
            return $template;
        } catch (\Exception $e) {
            $this->logger->error('更新模板失败: {message}', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * 删除模板
     */
    public function delete(Template $template): bool
    {
        try {
            $request = new DeleteTemplateRequest();
            $request->setAccount($template->getAccount());
            $request->setTemplateId($template->getTplId());
    
            $this->apiClient->request($request);
            $this->entityManager->remove($template);
            $this->entityManager->flush();
            
            return true;
        } catch (\Exception $e) {
            $this->logger->error('删除模板失败: {message}', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);
            return false;
        }
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
