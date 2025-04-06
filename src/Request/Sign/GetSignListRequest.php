<?php

namespace YunpianSmsBundle\Request\Sign;

use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Request\AbstractRequest;

/**
 * @see https://www.yunpian.com/official/document/sms/zh_CN/domestic_sign_get
 */
class GetSignListRequest extends AbstractRequest
{
    private Account $account;

    public function getMethod(): string
    {
        return 'POST';
    }

    public function getUri(): string
    {
        return 'https://sms.yunpian.com/v2/sign/get.json';
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
        ];
    }

    public function setAccount(Account $account): self
    {
        $this->account = $account;
        return $this;
    }
}
