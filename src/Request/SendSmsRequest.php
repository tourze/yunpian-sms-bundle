<?php

namespace YunpianSmsBundle\Request;

use YunpianSmsBundle\Entity\Account;

class SendSmsRequest implements RequestInterface
{
    private string $apiKey;

    private string $mobile;

    private string $content;

    private ?string $uid = null;

    public function getRequestMethod(): ?string
    {
        return 'POST';
    }

    public function getRequestPath(): string
    {
        return 'https://sms.yunpian.com/v2/sms/single_send.json';
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getRequestOptions(): ?array
    {
        $params = [
            'apikey' => $this->apiKey,
            'mobile' => $this->mobile,
            'text' => $this->content,
        ];

        if (null !== $this->uid) {
            $params['uid'] = $this->uid;
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

    public function setMobile(string $mobile): void
    {
        $this->mobile = $mobile;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function setUid(?string $uid): void
    {
        $this->uid = $uid;
    }
}
