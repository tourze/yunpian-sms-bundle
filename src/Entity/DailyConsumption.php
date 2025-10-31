<?php

namespace YunpianSmsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use YunpianSmsBundle\Repository\DailyConsumptionRepository;

#[ORM\Entity(repositoryClass: DailyConsumptionRepository::class)]
#[ORM\Table(name: 'ims_yunpian_daily_consumption', options: ['comment' => '云片短信日消耗'])]
#[ORM\UniqueConstraint(name: 'uniq_account_date', columns: ['account_id', 'date'])]
class DailyConsumption implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[ORM\ManyToOne(targetEntity: Account::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private Account $account;

    #[Assert\NotNull(message: '消耗日期不能为空')]
    #[ORM\Column(type: Types::DATE_IMMUTABLE, options: ['comment' => '消耗日期'])]
    private \DateTimeImmutable $date;

    #[Assert\NotNull(message: '总计短信条数不能为空')]
    #[Assert\PositiveOrZero(message: '总计短信条数必须为非负数')]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '总计短信条数'])]
    private int $totalCount = 0;

    #[Assert\NotNull(message: '花费费用不能为空')]
    #[Assert\Regex(pattern: '/^\d+(\.\d{1,3})?$/', message: '花费费用格式不正确')]
    #[Assert\Length(max: 10, maxMessage: '花费费用长度不能超过 {{ limit }} 个字符')]
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 3, options: ['comment' => '花费费用'])]
    private string $totalFee = '0.000';

    #[Assert\NotNull(message: '成功短信条数不能为空')]
    #[Assert\PositiveOrZero(message: '成功短信条数必须为非负数')]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '成功短信条数'])]
    private int $totalSuccessCount = 0;

    #[Assert\NotNull(message: '失败短信条数不能为空')]
    #[Assert\PositiveOrZero(message: '失败短信条数必须为非负数')]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '失败短信条数'])]
    private int $totalFailedCount = 0;

    #[Assert\NotNull(message: '未知短信条数不能为空')]
    #[Assert\PositiveOrZero(message: '未知短信条数必须为非负数')]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '未知短信条数'])]
    private int $totalUnknownCount = 0;

    /**
     * @var array<array-key, mixed>|null
     */
    #[Assert\Type(type: 'array', message: '消费明细必须为数组类型')]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '消费明细'])]
    private ?array $items = null;

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

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): void
    {
        $this->date = $date instanceof \DateTimeImmutable ? $date : \DateTimeImmutable::createFromInterface($date);
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    public function setTotalCount(int $totalCount): void
    {
        $this->totalCount = $totalCount;
    }

    public function getTotalFee(): string
    {
        return $this->totalFee;
    }

    public function setTotalFee(string $totalFee): void
    {
        $this->totalFee = $totalFee;
    }

    public function getTotalSuccessCount(): int
    {
        return $this->totalSuccessCount;
    }

    public function setTotalSuccessCount(int $totalSuccessCount): void
    {
        $this->totalSuccessCount = $totalSuccessCount;
    }

    public function getTotalFailedCount(): int
    {
        return $this->totalFailedCount;
    }

    public function setTotalFailedCount(int $totalFailedCount): void
    {
        $this->totalFailedCount = $totalFailedCount;
    }

    public function getTotalUnknownCount(): int
    {
        return $this->totalUnknownCount;
    }

    public function setTotalUnknownCount(int $totalUnknownCount): void
    {
        $this->totalUnknownCount = $totalUnknownCount;
    }

    /**
     * @return array<array-key, mixed>|null
     */
    public function getItems(): ?array
    {
        return $this->items;
    }

    /**
     * @param array<array-key, mixed>|null $items
     */
    public function setItems(?array $items): void
    {
        $this->items = $items;
    }

    public function __toString(): string
    {
        return sprintf(
            '%s - %s (总计: %d条, 费用: %s)',
            $this->getAccount(),
            $this->getDate()->format('Y-m-d'),
            $this->getTotalCount(),
            $this->getTotalFee()
        );
    }
}
