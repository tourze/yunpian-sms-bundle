<?php

namespace YunpianSmsBundle\Request\Template;

use YunpianSmsBundle\Enum\NotifyTypeEnum;
use YunpianSmsBundle\Enum\TemplateTypeEnum;
use YunpianSmsBundle\Request\AccountAware;
use YunpianSmsBundle\Request\RequestInterface;

/**
 * @see https://www.yunpian.com/official/document/sms/zh_CN/domestic_tpl_add
 */
class AddTemplateRequest implements RequestInterface
{
    use AccountAware;

    public function getRequestPath(): string
    {
        return 'https://sms.yunpian.com/v2/tpl/add.json';
    }

    public function getRequestOptions(): ?array
    {
        $params = [
            'apikey' => $this->getAccount()->getApiKey(),
            'tpl_content' => $this->getContent(),
        ];
        if ($this->getNotifyType() !== null) {
            $params['notify_type'] = $this->getNotifyType()->value;
        }
        if ($this->getWebsite() !== null) {
            $params['website'] = $this->getWebsite();
        }
        if ($this->getTemplateType() !== null) {
            $params['tpl_type'] = $this->getTemplateType()->value;
        }
        if ($this->getCallback() !== null) {
            $params['callback'] = $this->getCallback();
        }
        if ($this->getApplyDescription() !== null) {
            $params['apply_description'] = $this->getApplyDescription();
        }

        return [
            'headers' => [
                'content-type' => 'application/x-www-form-urlencode',
                'Accept' => 'application/json;charset=utf-8',
            ],
            'form' => $params,
        ];
    }

    /**
     * @var string 模板内容，必须以带符号【】的签名开头
     */
    private string $content;

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @var NotifyTypeEnum|null 审核结果短信通知的方式
     */
    private NotifyTypeEnum|null $notifyType = null;

    public function getNotifyType(): ?NotifyTypeEnum
    {
        return $this->notifyType;
    }

    public function setNotifyType(?NotifyTypeEnum $notifyType): void
    {
        $this->notifyType = $notifyType;
    }

    /**
     * @var string|null 验证码类模板对应的官网注册页面，验证码类模板必填
     */
    private string|null $website;

    public function getWebsite(): string|null
    {
        return $this->website;
    }

    public function setWebsite(string|null $website): void
    {
        $this->website = $website;
    }

    /**
     * @var TemplateTypeEnum|null 模板类型
     */
    private TemplateTypeEnum|null $templateType = null;

    public function getTemplateType(): ?TemplateTypeEnum
    {
        return $this->templateType;
    }

    public function setTemplateType(?TemplateTypeEnum $templateType): void
    {
        $this->templateType = $templateType;
    }

    /**
     * @var string|null 审核结果会向这个地址推送
     */
    private string|null $callback = null;

    public function getCallback(): ?string
    {
        return $this->callback;
    }

    public function setCallback(?string $callback): void
    {
        $this->callback = $callback;
    }

    /**
     * @var string|null 说明模板的发送场景和对象，验证码类模板必填
     */
    private string|null $applyDescription = null;

    public function getApplyDescription(): ?string
    {
        return $this->applyDescription;
    }

    public function setApplyDescription(?string $applyDescription): void
    {
        $this->applyDescription = $applyDescription;
    }

    public function getRequestMethod(): ?string
    {
        return 'POST';
    }
}
