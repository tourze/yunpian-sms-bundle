<?php

namespace YunpianSmsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use YunpianSmsBundle\Enum\SendStatusEnum;
use YunpianSmsBundle\Repository\SendLogRepository;

#[ORM\Entity(repositoryClass: SendLogRepository::class)]
#[ORM\Table(name: 'ims_yunpian_send_log', options: ['comment' => '云片短信发送记录'])]
class SendLog
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

    #[ORM\ManyToOne(targetEntity: Template::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Template $template = null;

    #[ORM\Column(type: Types::STRING, length: 32, options: ['comment' => '手机号'])]
    private string $mobile;

    #[ORM\Column(type: Types::TEXT, options: ['comment' => '短信内容'])]
    private string $content;

    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '业务ID'])]
    private ?string $uid = null;

    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '云片短信ID'])]
    private ?string $sid = null;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '计费条数'])]
    private int $count = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 3, options: ['comment' => '花费费用'])]
    private string $fee = '0.000';

    #[ORM\Column(type: Types::STRING, enumType: SendStatusEnum::class, options: ['comment' => '发送状态'])]
    private SendStatusEnum $status = SendStatusEnum::PENDING;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '状态说明'])]
    private ?string $statusMsg = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '用户接收时间'])]
    private ?\DateTimeInterface $receiveTime = null;

    #[ORM\Column(type: Types::STRING, length: 32, nullable: true, options: ['comment' => '运营商错误码'])]
    private ?string $errorMsg = null;

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

    public function getTemplate(): ?Template
    {
        return $this->template;
    }

    public function setTemplate(?Template $template): self
    {
        $this->template = $template;
        return $this;
    }

    public function getMobile(): string
    {
        return $this->mobile;
    }

    public function setMobile(string $mobile): self
    {
        $this->mobile = $mobile;
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

    public function getUid(): ?string
    {
        return $this->uid;
    }

    public function setUid(?string $uid): self
    {
        $this->uid = $uid;
        return $this;
    }

    public function getSid(): ?string
    {
        return $this->sid;
    }

    public function setSid(?string $sid): self
    {
        $this->sid = $sid;
        return $this;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): self
    {
        $this->count = $count;
        return $this;
    }

    public function getFee(): string
    {
        return $this->fee;
    }

    public function setFee(string $fee): self
    {
        $this->fee = $fee;
        return $this;
    }

    public function getStatus(): SendStatusEnum
    {
        return $this->status;
    }

    public function setStatus(SendStatusEnum|string|null $status): self
    {
        if ($status instanceof SendStatusEnum) {
            $this->status = $status;
            return $this;
        }

        if ($status === null) {
            $this->status = SendStatusEnum::PENDING;
            return $this;
        }

        // 将云片的状态映射到我们的枚举
        $this->status = match ($status) {
            'success' => SendStatusEnum::SUCCESS,
            'fail' => SendStatusEnum::FAILED,
            'sending' => SendStatusEnum::SENDING,
            'delivered' => SendStatusEnum::DELIVERED,
            'undelivered' => SendStatusEnum::UNDELIVERED,
            default => SendStatusEnum::PENDING,
        };
        return $this;
    }

    public function getStatusMsg(): ?string
    {
        return $this->statusMsg;
    }

    public function setStatusMsg(?string $statusMsg): self
    {
        $this->statusMsg = $statusMsg;
        return $this;
    }

    public function getReceiveTime(): ?\DateTimeInterface
    {
        return $this->receiveTime;
    }

    public function setReceiveTime(?\DateTimeInterface $receiveTime): self
    {
        $this->receiveTime = $receiveTime;
        return $this;
    }

    public function getErrorMsg(): ?string
    {
        return $this->errorMsg;
    }

    public function setErrorMsg(?string $errorMsg): self
    {
        $this->errorMsg = $errorMsg;
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
