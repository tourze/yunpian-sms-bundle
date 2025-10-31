<?php

namespace YunpianSmsBundle\Request\Template;

use YunpianSmsBundle\Enum\NotifyTypeEnum;
use YunpianSmsBundle\Enum\TemplateTypeEnum;
use YunpianSmsBundle\Request\AccountAware;
use YunpianSmsBundle\Request\RequestInterface;

/**
 * @see https://www.yunpian.com/official/document/sms/zh_CN/domestic_tpl_update
 */
class UpdateTemplateRequest implements RequestInterface
{
    use AccountAware;

    public function getRequestPath(): string
    {
        return 'https://sms.yunpian.com/v2/tpl/update.json';
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getRequestOptions(): ?array
    {
        $params = [
            'apikey' => $this->getAccount()->getApiKey(),
            'tpl_id' => $this->getTplId(),
            'tpl_content' => $this->getContent(),
        ];
        if (null !== $this->getNotifyType()) {
            $params['notify_type'] = $this->getNotifyType()->value;
        }
        if (null !== $this->getWebsite()) {
            $params['website'] = $this->getWebsite();
        }
        if (null !== $this->getTemplateType()) {
            $params['tplType'] = $this->getTemplateType()->value;
            $params['tpl_type'] = $this->getTemplateType()->value;
        }
        if (null !== $this->getCallback()) {
            $params['callback'] = $this->getCallback();
        }
        if (null !== $this->getApplyDescription()) {
            $params['apply_description'] = $this->getApplyDescription();
        }

        return [
            'body' => http_build_query($params),
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
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
    private ?NotifyTypeEnum $notifyType = null;

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
    private ?string $website = null;

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): void
    {
        $this->website = $website;
    }

    /**
     * @var TemplateTypeEnum|null 模板类型
     */
    private ?TemplateTypeEnum $templateType = null;

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
    private ?string $callback = null;

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
    private ?string $applyDescription = null;

    public function getApplyDescription(): ?string
    {
        return $this->applyDescription;
    }

    public function setApplyDescription(?string $applyDescription): void
    {
        $this->applyDescription = $applyDescription;
    }

    /**
     * @var string 指定 id 时返回 id 对应的模板
     */
    private string $tplId;

    public function getTplId(): string
    {
        return $this->tplId;
    }

    public function setTplId(string $tplId): void
    {
        $this->tplId = $tplId;
    }

    public function getRequestMethod(): ?string
    {
        return 'POST';
    }
}
