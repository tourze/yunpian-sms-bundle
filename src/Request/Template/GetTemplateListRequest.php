<?php

namespace YunpianSmsBundle\Request\Template;

use YunpianSmsBundle\Request\AccountAware;
use YunpianSmsBundle\Request\RequestInterface;

/**
 * @see https://www.yunpian.com/official/document/sms/zh_CN/domestic_tpl_get
 */
class GetTemplateListRequest implements RequestInterface
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
            ]),
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ];
    }

    public function getRequestMethod(): ?string
    {
        return 'POST';
    }
}
