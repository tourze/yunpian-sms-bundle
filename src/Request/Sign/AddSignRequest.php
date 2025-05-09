<?php

namespace YunpianSmsBundle\Request\Sign;

use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Request\AbstractRequest;

/**
 * @see https://www.yunpian.com/official/document/sms/zh_CN/domestic_sign_add
 */
class AddSignRequest extends AbstractRequest
{
    private Account $account;
    private string $sign;
    private bool $notify = true;
    private bool $applyVip = false;
    private bool $isOnlyGlobal = false;
    private string $industryType = '其它';
    private ?int $proveType = null;
    private ?array $licenseUrls = null;
    private ?string $idCardName = null;
    private ?string $idCardNumber = null;
    private ?string $idCardFront = null;
    private ?string $idCardBack = null;
    private int $signUse = 0;

    public function getMethod(): string
    {
        return 'POST';
    }

    public function getUri(): string
    {
        return 'https://sms.yunpian.com/v2/sign/add.json';
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
        $body = [
            'apikey' => $this->account->getApiKey(),
            'sign' => $this->sign,
            'notify' => $this->notify ? 'true' : 'false',
            'apply_vip' => $this->applyVip ? 'true' : 'false',
            'is_only_global' => $this->isOnlyGlobal ? 'true' : 'false',
            'industry_type' => $this->industryType,
            'sign_use' => $this->signUse,
        ];

        if ($this->proveType !== null) {
            $body['prove_type'] = $this->proveType;
        }

        if ($this->licenseUrls !== null) {
            $body['license_urls'] = implode(',', $this->licenseUrls);
        }

        if ($this->idCardName !== null) {
            $body['id_card_name'] = $this->idCardName;
        }

        if ($this->idCardNumber !== null) {
            $body['id_card_number'] = $this->idCardNumber;
        }

        if ($this->idCardFront !== null) {
            $body['id_card_front'] = $this->idCardFront;
        }

        if ($this->idCardBack !== null) {
            $body['id_card_back'] = $this->idCardBack;
        }

        return $body;
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

    public function getSign(): string
    {
        return $this->sign;
    }

    public function setNotify(bool $notify): self
    {
        $this->notify = $notify;
        return $this;
    }

    public function setApplyVip(bool $applyVip): self
    {
        $this->applyVip = $applyVip;
        return $this;
    }

    public function setIsOnlyGlobal(bool $isOnlyGlobal): self
    {
        $this->isOnlyGlobal = $isOnlyGlobal;
        return $this;
    }

    public function setIndustryType(string $industryType): self
    {
        $this->industryType = $industryType;
        return $this;
    }

    public function setProveType(?int $proveType): self
    {
        $this->proveType = $proveType;
        return $this;
    }

    public function setLicenseUrls(?array $licenseUrls): self
    {
        $this->licenseUrls = $licenseUrls;
        return $this;
    }

    public function setIdCardName(?string $idCardName): self
    {
        $this->idCardName = $idCardName;
        return $this;
    }

    public function setIdCardNumber(?string $idCardNumber): self
    {
        $this->idCardNumber = $idCardNumber;
        return $this;
    }

    public function setIdCardFront(?string $idCardFront): self
    {
        $this->idCardFront = $idCardFront;
        return $this;
    }

    public function setIdCardBack(?string $idCardBack): self
    {
        $this->idCardBack = $idCardBack;
        return $this;
    }

    public function setSignUse(int $signUse): self
    {
        $this->signUse = $signUse;
        return $this;
    }
}
