<?php

namespace YunpianSmsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use YunpianSmsBundle\Repository\DailyConsumptionRepository;

#[ORM\Entity(repositoryClass: DailyConsumptionRepository::class)]
#[ORM\Table(name: 'ims_yunpian_daily_consumption', options: ['comment' => '云片短信日消耗'])]
#[ORM\UniqueConstraint(name: 'uniq_account_date', columns: ['account_id', 'date'])]
class DailyConsumption
{
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ORM\ManyToOne(targetEntity: Account::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Account $account;

    #[ORM\Column(type: Types::DATE_MUTABLE, options: ['comment' => '消耗日期'])]
    private \DateTimeInterface $date;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '总计短信条数'])]
    private int $totalCount = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 3, options: ['comment' => '花费费用'])]
    private string $totalFee = '0.000';

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '成功短信条数'])]
    private int $totalSuccessCount = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '失败短信条数'])]
    private int $totalFailedCount = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '未知短信条数'])]
    private int $totalUnknownCount = 0;

    #[Filterable]
    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[UpdateTimeColumn]
    #[ListColumn(order: 99, sorter: true)]
    #[Filterable]
    #[ExportColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '消费明细'])]
    private ?array $items = null;

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

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    public function setTotalCount(int $totalCount): self
    {
        $this->totalCount = $totalCount;
        return $this;
    }

    public function getTotalFee(): string
    {
        return $this->totalFee;
    }

    public function setTotalFee(string $totalFee): self
    {
        $this->totalFee = $totalFee;
        return $this;
    }

    public function getTotalSuccessCount(): int
    {
        return $this->totalSuccessCount;
    }

    public function setTotalSuccessCount(int $totalSuccessCount): self
    {
        $this->totalSuccessCount = $totalSuccessCount;
        return $this;
    }

    public function getTotalFailedCount(): int
    {
        return $this->totalFailedCount;
    }

    public function setTotalFailedCount(int $totalFailedCount): self
    {
        $this->totalFailedCount = $totalFailedCount;
        return $this;
    }

    public function getTotalUnknownCount(): int
    {
        return $this->totalUnknownCount;
    }

    public function setTotalUnknownCount(int $totalUnknownCount): self
    {
        $this->totalUnknownCount = $totalUnknownCount;
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

    public function getItems(): ?array
    {
        return $this->items;
    }

    public function setItems(?array $items): self
    {
        $this->items = $items;
        return $this;
    }
}
