<?php

namespace YunpianSmsBundle\Request;

class SendTplSmsRequest implements RequestInterface
{
    private string $apiKey;
    private string $mobile;
    private string $tplId;
    private array $tplValue = [];
    private ?string $uid = null;

    public function getRequestMethod(): ?string
    {
        return 'POST';
    }

    public function getRequestPath(): string
    {
        return '/v2/sms/tpl_single_send.json';
    }

    public function getRequestOptions(): ?array
    {
        $params = [
            'apikey' => $this->apiKey,
            'mobile' => $this->mobile,
            'tpl_id' => $this->tplId,
            'tpl_value' => urlencode(http_build_query($this->tplValue)),
        ];

        if ($this->uid) {
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

    public function setTplId(string $tplId): void
    {
        $this->tplId = $tplId;
    }

    public function setTplValue(array $tplValue): void
    {
        $this->tplValue = $tplValue;
    }

    public function setUid(?string $uid): void
    {
        $this->uid = $uid;
    }

    public function getTplId(): string
    {
        return $this->tplId;
    }
}
