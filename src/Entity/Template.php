<?php

namespace YunpianSmsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\EasyAdmin\Attribute\Column\BoolColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Field\FormField;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use YunpianSmsBundle\Enum\NotifyTypeEnum;
use YunpianSmsBundle\Enum\TemplateTypeEnum;
use YunpianSmsBundle\Repository\TemplateRepository;

#[ORM\Entity(repositoryClass: TemplateRepository::class)]
#[ORM\Table(name: 'ims_yunpian_template', options: ['comment' => '云片短信模板'])]
class Template
{
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[Groups(['restful_read', 'api_tree', 'admin_curd', 'api_list'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ORM\ManyToOne(targetEntity: Account::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Account $account;

    #[ORM\Column(type: Types::BIGINT, unique: true, options: ['comment' => '云片模板ID'])]
    private string $tplId;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '模板标题'])]
    private string $title;

    #[ORM\Column(type: Types::TEXT, options: ['comment' => '模板内容'])]
    private string $content;

    #[ORM\Column(type: Types::STRING, length: 32, options: ['comment' => '审核状态'])]
    private string $checkStatus;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '审核说明'])]
    private ?string $checkReply = null;

    #[ORM\Column(type: Types::INTEGER, enumType: NotifyTypeEnum::class, options: ['comment' => '审核结果通知方式', 'default' => 0])]
    private NotifyTypeEnum $notifyType = NotifyTypeEnum::ALWAYS;

    #[ORM\Column(type: Types::INTEGER, enumType: TemplateTypeEnum::class, options: ['comment' => '模板类型', 'default' => 0])]
    private TemplateTypeEnum $templateType = TemplateTypeEnum::NOTIFICATION;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '验证码模板对应的官网注册页面'])]
    private ?string $website = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '验证码模板说明'])]
    private ?string $applyDescription = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '审核结果地址'])]
    private ?string $callback = null;

    #[BoolColumn]
    #[IndexColumn]
    #[TrackColumn]
    #[Groups(['admin_curd', 'restful_read', 'restful_read', 'restful_write'])]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    #[ListColumn(order: 97)]
    #[FormField(order: 97)]
    private ?bool $valid = false;

    #[Filterable]
    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[Groups(['restful_read', 'admin_curd', 'restful_read'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[UpdateTimeColumn]
    #[ListColumn(order: 99, sorter: true)]
    #[Groups(['restful_read', 'admin_curd', 'restful_read'])]
    #[Filterable]
    #[ExportColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): self
    {
        $this->account = $account;
        return $this;
    }

    public function getTplId(): string
    {
        return $this->tplId;
    }

    public function setTplId(string $tplId): self
    {
        $this->tplId = $tplId;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getCheckStatus(): string
    {
        return $this->checkStatus;
    }

    public function setCheckStatus(string $checkStatus): self
    {
        $this->checkStatus = $checkStatus;
        return $this;
    }

    public function getCheckReply(): ?string
    {
        return $this->checkReply;
    }

    public function setCheckReply(?string $checkReply): self
    {
        $this->checkReply = $checkReply;
        return $this;
    }

    public function getNotifyType(): NotifyTypeEnum
    {
        return $this->notifyType;
    }

    public function setNotifyType(NotifyTypeEnum $notifyType): self
    {
        $this->notifyType = $notifyType;
        return $this;
    }

    public function getTemplateType(): TemplateTypeEnum
    {
        return $this->templateType;
    }

    public function setTemplateType(TemplateTypeEnum $templateType): self
    {
        $this->templateType = $templateType;
        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;
        return $this;
    }

    public function getApplyDescription(): ?string
    {
        return $this->applyDescription;
    }

    public function setApplyDescription(?string $applyDescription): self
    {
        $this->applyDescription = $applyDescription;
        return $this;
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

    public function setValid(?bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }

    public function setCreateTime(?\DateTimeInterface $createdAt): void
    {
        $this->createTime = $createdAt;
    }

    public function getCreateTime(): ?\DateTimeInterface
    {
        return $this->createTime;
    }

    public function setUpdateTime(?\DateTimeInterface $updateTime): void
    {
        $this->updateTime = $updateTime;
    }

    public function getUpdateTime(): ?\DateTimeInterface
    {
        return $this->updateTime;
    }
}
