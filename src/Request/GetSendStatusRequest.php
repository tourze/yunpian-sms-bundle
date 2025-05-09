<?php

namespace YunpianSmsBundle\Request;

use YunpianSmsBundle\Entity\Account;

class GetSendStatusRequest implements RequestInterface
{
    private string $apiKey;
    private array $sids = [];

    public function getRequestMethod(): string
    {
        return 'POST';
    }

    public function getRequestPath(): string
    {
        return '/v2/sms/pull_status.json';
    }

    public function getRequestOptions(): array
    {
        $params = [
            'apikey' => $this->apiKey
        ];
        
        if (!empty($this->sids)) {
            $params['sid'] = implode(',', $this->sids);
        }
        
        return [
            'form_params' => $params
        ];
    }

    public function setAccount(Account $account): void
    {
        $this->apiKey = $account->getApiKey();
    }
    
    public function setSids(array $sids): void
    {
        $this->sids = $sids;
    }
    
    public function getSids(): array
    {
        return $this->sids;
    }
}
