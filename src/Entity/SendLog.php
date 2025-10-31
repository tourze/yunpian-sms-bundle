<?php

namespace YunpianSmsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use YunpianSmsBundle\Enum\SendStatusEnum;
use YunpianSmsBundle\Repository\SendLogRepository;

#[ORM\Entity(repositoryClass: SendLogRepository::class)]
#[ORM\Table(name: 'ims_yunpian_send_log', options: ['comment' => '云片短信发送记录'])]
class SendLog implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[ORM\ManyToOne(targetEntity: Account::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private Account $account;

    #[ORM\ManyToOne(targetEntity: Template::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Template $template = null;

    #[Assert\NotBlank(message: '手机号不能为空')]
    #[Assert\Length(max: 32, maxMessage: '手机号长度不能超过 {{ limit }} 个字符')]
    #[Assert\Regex(pattern: '/^1[3-9]\d{9}$/', message: '手机号格式不正确')]
    #[ORM\Column(type: Types::STRING, length: 32, options: ['comment' => '手机号'])]
    private string $mobile;

    #[Assert\NotBlank(message: '短信内容不能为空')]
    #[Assert\Length(max: 1000, maxMessage: '短信内容长度不能超过 {{ limit }} 个字符')]
    #[ORM\Column(type: Types::TEXT, options: ['comment' => '短信内容'])]
    private string $content;

    #[Assert\Length(max: 64, maxMessage: '业务ID长度不能超过 {{ limit }} 个字符')]
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '业务ID'])]
    private ?string $uid = null;

    #[Assert\Length(max: 64, maxMessage: '云片短信ID长度不能超过 {{ limit }} 个字符')]
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '云片短信ID'])]
    private ?string $sid = null;

    #[Assert\NotNull(message: '计费条数不能为空')]
    #[Assert\PositiveOrZero(message: '计费条数必须为非负数')]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '计费条数'])]
    private int $count = 0;

    #[Assert\NotNull(message: '花费费用不能为空')]
    #[Assert\Regex(pattern: '/^\d+(\.\d{1,3})?$/', message: '花费费用格式不正确')]
    #[Assert\Length(max: 10, maxMessage: '花费费用长度不能超过 {{ limit }} 个字符')]
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 3, options: ['comment' => '花费费用'])]
    private string $fee = '0.000';

    #[Assert\NotNull(message: '发送状态不能为空')]
    #[Assert\Choice(callback: [SendStatusEnum::class, 'cases'], message: '发送状态无效')]
    #[ORM\Column(type: Types::STRING, enumType: SendStatusEnum::class, options: ['comment' => '发送状态'])]
    private SendStatusEnum $status = SendStatusEnum::PENDING;

    #[Assert\Length(max: 255, maxMessage: '状态说明长度不能超过 {{ limit }} 个字符')]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '状态说明'])]
    private ?string $statusMsg = null;

    #[Assert\Type(type: \DateTimeImmutable::class, message: '用户接收时间必须为有效的日期时间')]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '用户接收时间'])]
    private ?\DateTimeImmutable $receiveTime = null;

    #[Assert\Length(max: 32, maxMessage: '运营商错误码长度不能超过 {{ limit }} 个字符')]
    #[ORM\Column(type: Types::STRING, length: 32, nullable: true, options: ['comment' => '运营商错误码'])]
    private ?string $errorMsg = null;

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

    public function getTemplate(): ?Template
    {
        return $this->template;
    }

    public function setTemplate(?Template $template): void
    {
        $this->template = $template;
    }

    public function getMobile(): string
    {
        return $this->mobile;
    }

    public function setMobile(string $mobile): void
    {
        $this->mobile = $mobile;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getUid(): ?string
    {
        return $this->uid;
    }

    public function setUid(?string $uid): void
    {
        $this->uid = $uid;
    }

    public function getSid(): ?string
    {
        return $this->sid;
    }

    public function setSid(?string $sid): void
    {
        $this->sid = $sid;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    public function getFee(): string
    {
        return $this->fee;
    }

    public function setFee(string $fee): void
    {
        $this->fee = $fee;
    }

    public function getStatus(): SendStatusEnum
    {
        return $this->status;
    }

    public function setStatus(SendStatusEnum|string|null $status): void
    {
        if ($status instanceof SendStatusEnum) {
            $this->status = $status;

            return;
        }

        if (null === $status) {
            $this->status = SendStatusEnum::PENDING;

            return;
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
    }

    public function getStatusMsg(): ?string
    {
        return $this->statusMsg;
    }

    public function setStatusMsg(?string $statusMsg): void
    {
        $this->statusMsg = $statusMsg;
    }

    public function getReceiveTime(): ?\DateTimeImmutable
    {
        return $this->receiveTime;
    }

    public function setReceiveTime(?\DateTimeInterface $receiveTime): void
    {
        if (null === $receiveTime) {
            $this->receiveTime = null;
        } else {
            $this->receiveTime = $receiveTime instanceof \DateTimeImmutable ? $receiveTime : \DateTimeImmutable::createFromInterface($receiveTime);
        }
    }

    public function getErrorMsg(): ?string
    {
        return $this->errorMsg;
    }

    public function setErrorMsg(?string $errorMsg): void
    {
        $this->errorMsg = $errorMsg;
    }

    public function __toString(): string
    {
        return sprintf(
            '%s - %s (%s)',
            $this->getMobile(),
            mb_substr($this->getContent(), 0, 20) . '...',
            $this->getStatus()->value
        );
    }
}
