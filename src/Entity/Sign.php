<?php

namespace YunpianSmsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use YunpianSmsBundle\Repository\SignRepository;

#[ORM\Entity(repositoryClass: SignRepository::class)]
#[ORM\Table(name: 'ims_yunpian_sign', options: ['comment' => '云片短信签名'])]
class Sign implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[ORM\ManyToOne(targetEntity: Account::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private Account $account;

    #[Assert\Type(type: 'integer', message: '云片签名ID必须为整数类型')]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '云片签名ID'])]
    private ?int $signId = null;

    #[Assert\NotBlank(message: '签名内容不能为空')]
    #[Assert\Length(max: 64, maxMessage: '签名内容长度不能超过 {{ limit }} 个字符')]
    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '签名内容'])]
    private string $sign;

    #[Assert\NotBlank(message: '审核状态不能为空')]
    #[Assert\Length(max: 32, maxMessage: '审核状态长度不能超过 {{ limit }} 个字符')]
    #[ORM\Column(type: Types::STRING, length: 32, options: ['comment' => '审核状态'])]
    private string $applyState;

    #[Assert\Length(max: 255, maxMessage: '业务网址长度不能超过 {{ limit }} 个字符')]
    #[Assert\Url(message: '业务网址格式不正确')]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '业务网址'])]
    private ?string $website = null;

    #[Assert\Type(type: 'bool', message: '是否短信通知结果必须为布尔类型')]
    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否短信通知结果', 'default' => true])]
    private bool $notify = true;

    #[Assert\Type(type: 'bool', message: '是否申请专用通道必须为布尔类型')]
    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否申请专用通道', 'default' => false])]
    private bool $applyVip = false;

    #[Assert\Type(type: 'bool', message: '是否发国际短信必须为布尔类型')]
    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否发国际短信', 'default' => false])]
    private bool $isOnlyGlobal = false;

    #[Assert\NotBlank(message: '所属行业不能为空')]
    #[Assert\Length(max: 64, maxMessage: '所属行业长度不能超过 {{ limit }} 个字符')]
    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '所属行业', 'default' => '其它'])]
    private string $industryType = '其它';

    #[Assert\Type(type: 'integer', message: '证明文件类型必须为整数类型')]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '证明文件类型'])]
    private ?int $proveType = null;

    /**
     * @var array<int, string>|null
     */
    #[Assert\Type(type: 'array', message: '证明文件URL列表必须为数组类型')]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '证明文件URL列表'])]
    private ?array $licenseUrls = null;

    #[Assert\Length(max: 64, maxMessage: '企业责任人姓名长度不能超过 {{ limit }} 个字符')]
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '企业责任人姓名'])]
    private ?string $idCardName = null;

    #[Assert\Length(max: 32, maxMessage: '企业责任人身份证号长度不能超过 {{ limit }} 个字符')]
    #[ORM\Column(type: Types::STRING, length: 32, nullable: true, options: ['comment' => '企业责任人身份证号'])]
    private ?string $idCardNumber = null;

    #[Assert\Length(max: 65535, maxMessage: '身份证正面照片长度不能超过 {{ limit }} 个字符')]
    #[Assert\Url(message: '身份证正面照片URL格式不正确')]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '身份证正面照片'])]
    private ?string $idCardFront = null;

    #[Assert\Length(max: 65535, maxMessage: '身份证反面照片长度不能超过 {{ limit }} 个字符')]
    #[Assert\Url(message: '身份证反面照片URL格式不正确')]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '身份证反面照片'])]
    private ?string $idCardBack = null;

    #[Assert\Type(type: 'integer', message: '签名用途必须为整数类型')]
    #[Assert\Choice(choices: [0, 1], message: '签名用途只能是 0（自用）或 1（他用）')]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '签名用途 0:自用|1:他用', 'default' => 0])]
    private int $signUse = 0;

    #[IndexColumn]
    #[TrackColumn]
    #[Assert\Type(type: 'bool', message: '有效性必须为布尔类型')]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;

    #[Assert\Length(max: 255, maxMessage: '备注长度不能超过 {{ limit }} 个字符')]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '备注'])]
    private ?string $remark = null;

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

    public function getSignId(): ?int
    {
        return $this->signId;
    }

    public function setSignId(?int $signId): void
    {
        $this->signId = $signId;
    }

    public function getSign(): string
    {
        return $this->sign;
    }

    public function setSign(string $sign): void
    {
        $this->sign = $sign;
    }

    public function getApplyState(): string
    {
        return $this->applyState;
    }

    public function setApplyState(string $applyState): void
    {
        $this->applyState = $applyState;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): void
    {
        $this->website = $website;
    }

    public function isNotify(): bool
    {
        return $this->notify;
    }

    public function setNotify(bool $notify): void
    {
        $this->notify = $notify;
    }

    public function isApplyVip(): bool
    {
        return $this->applyVip;
    }

    public function setApplyVip(bool $applyVip): void
    {
        $this->applyVip = $applyVip;
    }

    public function isOnlyGlobal(): bool
    {
        return $this->isOnlyGlobal;
    }

    public function setIsOnlyGlobal(bool $isOnlyGlobal): void
    {
        $this->isOnlyGlobal = $isOnlyGlobal;
    }

    public function getIndustryType(): string
    {
        return $this->industryType;
    }

    public function setIndustryType(string $industryType): void
    {
        $this->industryType = $industryType;
    }

    public function getProveType(): ?int
    {
        return $this->proveType;
    }

    public function setProveType(?int $proveType): void
    {
        $this->proveType = $proveType;
    }

    /**
     * @return array<int, string>|null
     */
    public function getLicenseUrls(): ?array
    {
        return $this->licenseUrls;
    }

    /**
     * @param array<int, string>|null $licenseUrls
     */
    public function setLicenseUrls(?array $licenseUrls): void
    {
        $this->licenseUrls = $licenseUrls;
    }

    public function getIdCardName(): ?string
    {
        return $this->idCardName;
    }

    public function setIdCardName(?string $idCardName): void
    {
        $this->idCardName = $idCardName;
    }

    public function getIdCardNumber(): ?string
    {
        return $this->idCardNumber;
    }

    public function setIdCardNumber(?string $idCardNumber): void
    {
        $this->idCardNumber = $idCardNumber;
    }

    public function getIdCardFront(): ?string
    {
        return $this->idCardFront;
    }

    public function setIdCardFront(?string $idCardFront): void
    {
        $this->idCardFront = $idCardFront;
    }

    public function getIdCardBack(): ?string
    {
        return $this->idCardBack;
    }

    public function setIdCardBack(?string $idCardBack): void
    {
        $this->idCardBack = $idCardBack;
    }

    public function getSignUse(): int
    {
        return $this->signUse;
    }

    public function setSignUse(int $signUse): void
    {
        $this->signUse = $signUse;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): void
    {
        $this->valid = $valid;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): void
    {
        $this->remark = $remark;
    }

    public function __toString(): string
    {
        return sprintf(
            '%s (%s)',
            $this->getSign(),
            $this->getApplyState()
        );
    }
}
