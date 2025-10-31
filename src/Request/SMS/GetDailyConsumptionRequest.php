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
        return 'https://sms.yunpian.com/v2/sms/get_total_fee.json';
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getRequestOptions(): ?array
    {
        $params = [
            'apikey' => $this->apiKey,
            'date' => $this->date->format('Y-m-d'),
        ];

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

    public function setDate(\DateTimeInterface $date): void
    {
        $this->date = $date;
    }
}
