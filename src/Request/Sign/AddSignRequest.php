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

    /**
     * @var array<string>|null
     */
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

        return array_merge($body, $this->addOptionalFields());
    }

    /**
     * @return array<string, mixed>
     */
    private function addOptionalFields(): array
    {
        $optionalFields = [];

        if (null !== $this->proveType) {
            $optionalFields['prove_type'] = $this->proveType;
        }

        if (null !== $this->licenseUrls) {
            $optionalFields['license_urls'] = implode(',', $this->licenseUrls);
        }

        if (null !== $this->idCardName) {
            $optionalFields['id_card_name'] = $this->idCardName;
        }

        if (null !== $this->idCardNumber) {
            $optionalFields['id_card_number'] = $this->idCardNumber;
        }

        if (null !== $this->idCardFront) {
            $optionalFields['id_card_front'] = $this->idCardFront;
        }

        if (null !== $this->idCardBack) {
            $optionalFields['id_card_back'] = $this->idCardBack;
        }

        return $optionalFields;
    }

    public function setAccount(Account $account): void
    {
        $this->account = $account;
    }

    public function setSign(string $sign): void
    {
        $this->sign = $sign;
    }

    public function getSign(): string
    {
        return $this->sign;
    }

    public function setNotify(bool $notify): void
    {
        $this->notify = $notify;
    }

    public function setApplyVip(bool $applyVip): void
    {
        $this->applyVip = $applyVip;
    }

    public function setIsOnlyGlobal(bool $isOnlyGlobal): void
    {
        $this->isOnlyGlobal = $isOnlyGlobal;
    }

    public function setIndustryType(string $industryType): void
    {
        $this->industryType = $industryType;
    }

    public function setProveType(?int $proveType): void
    {
        $this->proveType = $proveType;
    }

    /**
     * @param array<string>|null $licenseUrls
     */
    public function setLicenseUrls(?array $licenseUrls): void
    {
        $this->licenseUrls = $licenseUrls;
    }

    public function setIdCardName(?string $idCardName): void
    {
        $this->idCardName = $idCardName;
    }

    public function setIdCardNumber(?string $idCardNumber): void
    {
        $this->idCardNumber = $idCardNumber;
    }

    public function setIdCardFront(?string $idCardFront): void
    {
        $this->idCardFront = $idCardFront;
    }

    public function setIdCardBack(?string $idCardBack): void
    {
        $this->idCardBack = $idCardBack;
    }

    public function setSignUse(int $signUse): void
    {
        $this->signUse = $signUse;
    }
}
