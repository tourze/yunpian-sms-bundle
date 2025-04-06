<?php

namespace YunpianSmsBundle\Request;

class GetSendRecordRequest implements RequestInterface
{
    private string $apiKey;
    private \DateTimeInterface $startTime;
    private \DateTimeInterface $endTime;
    private ?string $mobile = null;
    private int $pageNum = 1;
    private int $pageSize = 100;

    public function getRequestMethod(): ?string
    {
        return 'POST';
    }

    public function getRequestPath(): string
    {
        return '/v2/sms/get_record.json';
    }

    public function getRequestOptions(): ?array
    {
        $params = [
            'apikey' => $this->apiKey,
            'start_time' => $this->startTime->format('Y-m-d H:i:s'),
            'end_time' => $this->endTime->format('Y-m-d H:i:s'),
            'page_num' => $this->pageNum,
            'page_size' => $this->pageSize,
        ];

        if ($this->mobile) {
            $params['mobile'] = $this->mobile;
        }

        return [
            'form_params' => $params,
        ];
    }

    public function setAccount(\YunpianSmsBundle\Entity\Account $account): void
    {
        $this->apiKey = $account->getApiKey();
    }

    public function setStartTime(\DateTimeInterface $startTime): void
    {
        $this->startTime = $startTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): void
    {
        $this->endTime = $endTime;
    }

    public function setMobile(?string $mobile): void
    {
        $this->mobile = $mobile;
    }

    public function setPageNum(int $pageNum): void
    {
        $this->pageNum = $pageNum;
    }

    public function setPageSize(int $pageSize): void
    {
        $this->pageSize = $pageSize;
    }
}
