<?php

namespace YunpianSmsBundle\Request\SMS;

use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Request\RequestInterface;

/**
 * @see https://www.yunpian.com/official/document/sms/zh_cn/domestic_daily_cost
 */
class GetDailyConsumptionRequest implements RequestInterface
{
    private string $apiKey;
    private \DateTimeInterface $date;

    public function getRequestMethod(): ?string
    {
        return 'POST';
    }

    public function getRequestPath(): string
    {
        return '/v2/sms/get_total_fee.json';
    }

    public function getRequestOptions(): ?array
    {
        return [
            'form_params' => [
                'apikey' => $this->apiKey,
                'date' => $this->date->format('Y-m-d'),
            ],
        ];
    }

    public function setAccount(Account $account): void
    {
        $this->apiKey = $account->getApiKey();
    }

    public function setDate(\DateTimeInterface $date): void
    {
        $this->date = $date;
    }
}
