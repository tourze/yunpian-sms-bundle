<?php

namespace YunpianSmsBundle\Request\Sign;

use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Request\AbstractRequest;

class DeleteSignRequest extends AbstractRequest
{
    private Account $account;
    private string $sign;

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
        return [
            'apikey' => $this->account->getApiKey(),
            'sign' => $this->sign,
        ];
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
}
