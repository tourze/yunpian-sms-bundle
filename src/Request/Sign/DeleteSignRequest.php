<?php

namespace YunpianSmsBundle\Request\Sign;

use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Request\AbstractRequest;

class DeleteSignRequest extends AbstractRequest
{
    private Account $account;
    private string $sign;
    private ?int $signId = null;

    public function getMethod(): string
    {
        return 'POST';
    }

    public function getUri(): string
    {
        return 'https://sms.yunpian.com/v2/sign/del.json';
    }

    public function getHeaders(): array
    {
        return [
            'Accept' => 'application/json;charset=utf-8',
            'Content-Type' => 'application/x-www-form-urlencoded;charset=utf-8',
        ];
    }

    public function getBody(): array
    {
        $body = [
            'apikey' => $this->account->getApiKey(),
        ];
        
        if ($this->signId !== null) {
            $body['sign_id'] = $this->signId;
        } else {
            $body['sign'] = $this->sign;
        }
        
        return $body;
    }

    public function setAccount(Account $account): self
    {
        $this->account = $account;
        return $this;
    }

    public function setSign(string $sign): self
    {
        $this->sign = $sign;
        return $this;
    }
    
    public function getSign(): string
    {
        return $this->sign;
    }
    
    public function setSignId(int $signId): self
    {
        $this->signId = $signId;
        return $this;
    }
    
    public function getSignId(): ?int
    {
        return $this->signId;
    }
}
