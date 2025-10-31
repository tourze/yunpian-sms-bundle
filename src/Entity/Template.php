<?php

namespace YunpianSmsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use YunpianSmsBundle\Enum\NotifyTypeEnum;
use YunpianSmsBundle\Enum\TemplateTypeEnum;
use YunpianSmsBundle\Repository\TemplateRepository;

#[ORM\Entity(repositoryClass: TemplateRepository::class)]
#[ORM\Table(name: 'ims_yunpian_template', options: ['comment' => '云片短信模板'])]
class Template implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[ORM\ManyToOne(targetEntity: Account::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private Account $account;

    #[Assert\NotBlank(message: '云片模板ID不能为空')]
    #[Assert\Type(type: 'numeric', message: '云片模板ID必须为数字类型')]
    #[Assert\Length(max: 20, maxMessage: '云片模板ID长度不能超过 {{ limit }} 个字符')]
    #[ORM\Column(type: Types::BIGINT, unique: true, options: ['comment' => '云片模板ID'])]
    private string $tplId;

    #[Assert\NotBlank(message: '模板标题不能为空')]
    #[Assert\Length(max: 255, maxMessage: '模板标题长度不能超过 {{ limit }} 个字符')]
    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '模板标题'])]
    private string $title;

    #[Assert\NotBlank(message: '模板内容不能为空')]
    #[Assert\Length(max: 65535, maxMessage: '模板内容长度不能超过 {{ limit }} 个字符')]
    #[ORM\Column(type: Types::TEXT, options: ['comment' => '模板内容'])]
    private string $content;

    #[Assert\NotBlank(message: '审核状态不能为空')]
    #[Assert\Length(max: 32, maxMessage: '审核状态长度不能超过 {{ limit }} 个字符')]
    #[ORM\Column(type: Types::STRING, length: 32, options: ['comment' => '审核状态'])]
    private string $checkStatus;

    #[Assert\Length(max: 255, maxMessage: '审核说明长度不能超过 {{ limit }} 个字符')]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '审核说明'])]
    private ?string $checkReply = null;

    #[Assert\Choice(callback: [NotifyTypeEnum::class, 'cases'], message: '审核结果通知方式无效')]
    #[ORM\Column(type: Types::INTEGER, enumType: NotifyTypeEnum::class, options: ['comment' => '审核结果通知方式', 'default' => 0])]
    private NotifyTypeEnum $notifyType = NotifyTypeEnum::ALWAYS;

    #[Assert\Choice(callback: [TemplateTypeEnum::class, 'cases'], message: '模板类型无效')]
    #[ORM\Column(type: Types::INTEGER, enumType: TemplateTypeEnum::class, options: ['comment' => '模板类型', 'default' => 0])]
    private TemplateTypeEnum $templateType = TemplateTypeEnum::NOTIFICATION;

    #[Assert\Length(max: 255, maxMessage: '验证码模板对应的官网注册页面长度不能超过 {{ limit }} 个字符')]
    #[Assert\Url(message: '验证码模板对应的官网注册页面格式不正确')]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '验证码模板对应的官网注册页面'])]
    private ?string $website = null;

    #[Assert\Length(max: 255, maxMessage: '验证码模板说明长度不能超过 {{ limit }} 个字符')]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '验证码模板说明'])]
    private ?string $applyDescription = null;

    #[Assert\Length(max: 65535, maxMessage: '审核结果地址长度不能超过 {{ limit }} 个字符')]
    #[Assert\Url(message: '审核结果地址格式不正确')]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '审核结果地址'])]
    private ?string $callback = null;

    #[IndexColumn]
    #[TrackColumn]
    #[Assert\Type(type: 'bool', message: '有效性必须为布尔类型')]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;

    public function getId(): int
    {
        return $this->id;
    }

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): void
    {
        $this->account = $account;
    }

    public function getTplId(): string
    {
        return $this->tplId;
    }

    public function setTplId(string $tplId): void
    {
        $this->tplId = $tplId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getCheckStatus(): string
    {
        return $this->checkStatus;
    }

    public function setCheckStatus(string $checkStatus): void
    {
        $this->checkStatus = $checkStatus;
    }

    public function getCheckReply(): ?string
    {
        return $this->checkReply;
    }

    public function setCheckReply(?string $checkReply): void
    {
        $this->checkReply = $checkReply;
    }

    public function getNotifyType(): NotifyTypeEnum
    {
        return $this->notifyType;
    }

    public function setNotifyType(NotifyTypeEnum $notifyType): void
    {
        $this->notifyType = $notifyType;
    }

    public function getTemplateType(): TemplateTypeEnum
    {
        return $this->templateType;
    }

    public function setTemplateType(TemplateTypeEnum $templateType): void
    {
        $this->templateType = $templateType;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): void
    {
        $this->website = $website;
    }

    public function getApplyDescription(): ?string
    {
        return $this->applyDescription;
    }

    public function setApplyDescription(?string $applyDescription): void
    {
        $this->applyDescription = $applyDescription;
    }

    public function getCallback(): ?string
    {
        return $this->callback;
    }

    public function setCallback(?string $callback): void
    {
        $this->callback = $callback;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): void
    {
        $this->valid = $valid;
    }

    public function __toString(): string
    {
        return sprintf(
            '%s - %s (%s)',
            $this->getTplId(),
            $this->getTitle(),
            $this->getCheckStatus()
        );
    }
}
