<?php

namespace YunpianSmsBundle\Request;

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
        return '/v2/sms/single_send.json';
    }

    public function getRequestOptions(): ?array
    {
        $params = [
            'apikey' => $this->apiKey,
            'mobile' => $this->mobile,
            'text' => $this->content,
        ];

        if ($this->uid !== null) {
            $params['uid'] = $this->uid;
        }

        return [
            'form_params' => $params,
        ];
    }

    public function setAccount(\YunpianSmsBundle\Entity\Account $account): void
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
