<?php

namespace YunpianSmsBundle\Request\Template;

use YunpianSmsBundle\Request\AccountAware;
use YunpianSmsBundle\Request\RequestInterface;

/**
 * @see https://www.yunpian.com/official/document/sms/zh_CN/domestic_tpl_delete
 */
class DeleteTemplateRequest implements RequestInterface
{
    use AccountAware;

    public function getRequestPath(): string
    {
        return 'https://sms.yunpian.com/v2/tpl/del.json';
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getRequestOptions(): ?array
    {
        return [
            'body' => http_build_query([
                'apikey' => $this->account->getApiKey(),
                'tpl_id' => $this->getTemplateId(),
            ]),
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ];
    }

    private string $templateId;

    public function getTemplateId(): string
    {
        return $this->templateId;
    }

    public function setTemplateId(string $templateId): void
    {
        $this->templateId = $templateId;
    }

    public function getRequestMethod(): ?string
    {
        return 'POST';
    }
}
