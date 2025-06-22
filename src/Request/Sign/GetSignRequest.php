<?php

namespace YunpianSmsBundle\Request\Sign;

use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Request\RequestInterface;

class GetSignRequest implements RequestInterface
{
    private string $apiKey;
    private ?int $signId = null;
    private ?string $sign = null;

    public function getRequestMethod(): string
    {
        return 'POST';
    }

    public function getRequestPath(): string
    {
        return '/v2/sign/get.json';
    }

    public function getRequestOptions(): array
    {
        $params = [
            'apikey' => $this->apiKey
        ];
        
        if ($this->signId !== null) {
            $params['sign_id'] = (string)$this->signId;
        }
        
        if ($this->sign !== null) {
            $params['sign'] = $this->sign;
        }
        
        return [
            'form_params' => $params
        ];
    }

    public function setAccount(Account $account): void
    {
        $this->apiKey = $account->getApiKey();
    }
    
    public function setSignId(?int $signId): void
    {
        $this->signId = $signId;
    }
    
    public function getSignId(): ?int
    {
        return $this->signId;
    }
    
    public function setSign(?string $sign): void
    {
        $this->sign = $sign;
    }
    
    public function getSign(): ?string
    {
        return $this->sign;
    }
} 