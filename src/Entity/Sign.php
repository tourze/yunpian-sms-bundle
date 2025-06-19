<?php

namespace YunpianSmsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use YunpianSmsBundle\Repository\SignRepository;

#[ORM\Entity(repositoryClass: SignRepository::class)]
#[ORM\Table(name: 'ims_yunpian_sign', options: ['comment' => '云片短信签名'])]
class Sign implements Stringable
{
    use TimestampableAware;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ORM\ManyToOne(targetEntity: Account::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Account $account;

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '云片签名ID'])]
    private ?int $signId = null;

    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '签名内容'])]
    private string $sign;

    #[ORM\Column(type: Types::STRING, length: 32, options: ['comment' => '审核状态'])]
    private string $applyState;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '业务网址'])]
    private ?string $website = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否短信通知结果', 'default' => true])]
    private bool $notify = true;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否申请专用通道', 'default' => false])]
    private bool $applyVip = false;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否发国际短信', 'default' => false])]
    private bool $isOnlyGlobal = false;

    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '所属行业', 'default' => '其它'])]
    private string $industryType = '其它';

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '证明文件类型'])]
    private ?int $proveType = null;

    /**
     * @var array<int, string>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '证明文件URL列表'])]
    private ?array $licenseUrls = null;

    #[ORM\Column(type: Types::STRING, length: 64, nullable: true, options: ['comment' => '企业责任人姓名'])]
    private ?string $idCardName = null;

    #[ORM\Column(type: Types::STRING, length: 32, nullable: true, options: ['comment' => '企业责任人身份证号'])]
    private ?string $idCardNumber = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '身份证正面照片'])]
    private ?string $idCardFront = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '身份证反面照片'])]
    private ?string $idCardBack = null;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '签名用途 0:自用|1:他用', 'default' => 0])]
    private int $signUse = 0;

    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '备注'])]
    private ?string $remark = null;

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

    public function getSignId(): ?int
    {
        return $this->signId;
    }

    public function setSignId(?int $signId): self
    {
        $this->signId = $signId;
        return $this;
    }

    public function getSign(): string
    {
        return $this->sign;
    }

    public function setSign(string $sign): self
    {
        $this->sign = $sign;
        return $this;
    }

    public function getApplyState(): string
    {
        return $this->applyState;
    }

    public function setApplyState(string $applyState): self
    {
        $this->applyState = $applyState;
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

    public function isNotify(): bool
    {
        return $this->notify;
    }

    public function setNotify(bool $notify): self
    {
        $this->notify = $notify;
        return $this;
    }

    public function isApplyVip(): bool
    {
        return $this->applyVip;
    }

    public function setApplyVip(bool $applyVip): self
    {
        $this->applyVip = $applyVip;
        return $this;
    }

    public function isOnlyGlobal(): bool
    {
        return $this->isOnlyGlobal;
    }

    public function setIsOnlyGlobal(bool $isOnlyGlobal): self
    {
        $this->isOnlyGlobal = $isOnlyGlobal;
        return $this;
    }

    public function getIndustryType(): string
    {
        return $this->industryType;
    }

    public function setIndustryType(string $industryType): self
    {
        $this->industryType = $industryType;
        return $this;
    }

    public function getProveType(): ?int
    {
        return $this->proveType;
    }

    public function setProveType(?int $proveType): self
    {
        $this->proveType = $proveType;
        return $this;
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
    public function setLicenseUrls(?array $licenseUrls): self
    {
        $this->licenseUrls = $licenseUrls;
        return $this;
    }

    public function getIdCardName(): ?string
    {
        return $this->idCardName;
    }

    public function setIdCardName(?string $idCardName): self
    {
        $this->idCardName = $idCardName;
        return $this;
    }

    public function getIdCardNumber(): ?string
    {
        return $this->idCardNumber;
    }

    public function setIdCardNumber(?string $idCardNumber): self
    {
        $this->idCardNumber = $idCardNumber;
        return $this;
    }

    public function getIdCardFront(): ?string
    {
        return $this->idCardFront;
    }

    public function setIdCardFront(?string $idCardFront): self
    {
        $this->idCardFront = $idCardFront;
        return $this;
    }

    public function getIdCardBack(): ?string
    {
        return $this->idCardBack;
    }

    public function setIdCardBack(?string $idCardBack): self
    {
        $this->idCardBack = $idCardBack;
        return $this;
    }

    public function getSignUse(): int
    {
        return $this->signUse;
    }

    public function setSignUse(int $signUse): self
    {
        $this->signUse = $signUse;
        return $this;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): self
    {
        $this->remark = $remark;
        return $this;
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
