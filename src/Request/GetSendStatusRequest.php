<?php

namespace YunpianSmsBundle\Request;

use YunpianSmsBundle\Entity\Account;

class GetSendStatusRequest implements RequestInterface
{
    private string $apiKey;

    /**
     * @var array<string>
     */
    private array $sids = [];

    public function getRequestMethod(): string
    {
        return 'POST';
    }

    public function getRequestPath(): string
    {
        return 'https://sms.yunpian.com/v2/sms/pull_status.json';
    }

    /**
     * @return array<string, mixed>
     */
    public function getRequestOptions(): array
    {
        $params = [
            'apikey' => $this->apiKey,
        ];

        if ([] !== $this->sids) {
            $params['sid'] = implode(',', $this->sids);
        }

        return [
            'body' => http_build_query($params),
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ];
    }

    public function setAccount(Account $account): void
    {
        $this->apiKey = $account->getApiKey();
    }

    /**
     * @param array<string> $sids
     */
    public function setSids(array $sids): void
    {
        $this->sids = $sids;
    }

    /**
     * @return array<string>
     */
    public function getSids(): array
    {
        return $this->sids;
    }
}
