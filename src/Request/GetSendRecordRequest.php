<?php

namespace YunpianSmsBundle\Request;

use YunpianSmsBundle\Entity\Account;

class GetSendRecordRequest implements RequestInterface
{
    private string $apiKey;

    private \DateTimeInterface $startTime;

    private \DateTimeInterface $endTime;

    private ?string $mobile = null;

    private ?string $pageNum = null;

    private ?string $pageSize = null;

    public function getRequestMethod(): string
    {
        return 'POST';
    }

    public function getRequestPath(): string
    {
        return 'https://sms.yunpian.com/v2/sms/get_record.json';
    }

    /**
     * @return array<string, mixed>
     */
    public function getRequestOptions(): array
    {
        $params = [
            'apikey' => $this->apiKey,
            'start_time' => $this->startTime->format('Y-m-d H:i:s'),
            'end_time' => $this->endTime->format('Y-m-d H:i:s'),
        ];

        if (null !== $this->mobile) {
            $params['mobile'] = $this->mobile;
        }

        if (null !== $this->pageNum) {
            $params['page_num'] = $this->pageNum;
        }

        if (null !== $this->pageSize) {
            $params['page_size'] = $this->pageSize;
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

    public function setStartTime(\DateTimeInterface $startTime): void
    {
        $this->startTime = $startTime;
    }

    public function getStartTime(): \DateTimeInterface
    {
        return $this->startTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): void
    {
        $this->endTime = $endTime;
    }

    public function getEndTime(): \DateTimeInterface
    {
        return $this->endTime;
    }

    public function setMobile(?string $mobile): void
    {
        $this->mobile = $mobile;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setPageNum(?string $pageNum): void
    {
        $this->pageNum = $pageNum;
    }

    public function setPageSize(?string $pageSize): void
    {
        $this->pageSize = $pageSize;
    }
}
