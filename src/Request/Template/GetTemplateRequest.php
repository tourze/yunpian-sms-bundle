<?php

namespace YunpianSmsBundle\Request\Template;

use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Request\RequestInterface;

class GetTemplateRequest implements RequestInterface
{
    private string $apiKey;
    private ?string $tplId = null;

    public function getRequestMethod(): string
    {
        return 'POST';
    }

    public function getRequestPath(): string
    {
        return '/v2/tpl/get.json';
    }

    public function getRequestOptions(): array
    {
        $params = [
            'apikey' => $this->apiKey
        ];
        
        if ($this->tplId) {
            $params['tpl_id'] = $this->tplId;
        }
        
        return [
            'form_params' => $params
        ];
    }

    public function setAccount(Account $account): void
    {
        $this->apiKey = $account->getApiKey();
    }
    
    public function setTplId(?string $tplId): void
    {
        $this->tplId = $tplId;
    }
    
    public function getTplId(): ?string
    {
        return $this->tplId;
    }
} 