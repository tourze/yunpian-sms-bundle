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
        return 'https://sms.yunpian.com/v2/tpl/get.json';
    }

    /**
     * @return array<string, mixed>
     */
    public function getRequestOptions(): array
    {
        $params = [
            'apikey' => $this->apiKey,
        ];

        if (null !== $this->tplId) {
            $params['tpl_id'] = $this->tplId;
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

    public function setTplId(?string $tplId): void
    {
        $this->tplId = $tplId;
    }

    public function getTplId(): ?string
    {
        return $this->tplId;
    }
}
