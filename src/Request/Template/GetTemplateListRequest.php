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

    public function getRequestOptions(): ?array
    {
        return [
            'headers' => [
                'content-type' => 'application/x-www-form-urlencode',
                'Accept' => 'application/json;charset=utf-8',
            ],
            'form' => [
                'apikey' => $this->account->getApiKey(),
            ],
        ];
    }

    public function getRequestMethod(): ?string
    {
        return 'POST';
    }
}
