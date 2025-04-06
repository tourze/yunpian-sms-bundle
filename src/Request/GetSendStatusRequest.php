<?php

namespace YunpianSmsBundle\Request;

use YunpianSmsBundle\Entity\Account;

class GetSendStatusRequest implements RequestInterface
{
    private string $apiKey;
    private array $sids = [];

    public function getRequestMethod(): ?string
    {
        return 'POST';
    }

    public function getRequestPath(): string
    {
        return '/v2/sms/pull_status.json';
    }

    public function getRequestOptions(): ?array
    {
        return [
            'form_params' => [
                'apikey' => $this->apiKey,
                'sid' => implode(',', $this->sids),
            ],
        ];
    }

    public function setAccount(Account $account): void
    {
        $this->apiKey = $account->getApiKey();
    }

    /**
     * @param string[] $sids
     */
    public function setSids(array $sids): void
    {
        $this->sids = $sids;
    }
}
