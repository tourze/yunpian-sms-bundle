<?php

namespace YunpianSmsBundle\Request;

use YunpianSmsBundle\Entity\Account;

class SendTplSmsRequest implements RequestInterface
{
    private string $apiKey;

    private string $mobile;

    private string $tplId;

    /**
     * @var array<string, mixed>
     */
    private array $tplValue = [];

    private ?string $uid = null;

    public function getRequestMethod(): ?string
    {
        return 'POST';
    }

    public function getRequestPath(): string
    {
        return 'https://sms.yunpian.com/v2/sms/tpl_single_send.json';
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getRequestOptions(): ?array
    {
        $params = [
            'apikey' => $this->apiKey,
            'mobile' => $this->mobile,
            'tpl_id' => $this->tplId,
            'tpl_value' => urlencode(http_build_query($this->tplValue)),
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

    public function setTplId(string $tplId): void
    {
        $this->tplId = $tplId;
    }

    /**
     * @param array<string, mixed> $tplValue
     */
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
