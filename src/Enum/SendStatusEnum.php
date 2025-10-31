<?php

namespace YunpianSmsBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum SendStatusEnum: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case PENDING = 'pending';
    case SENDING = 'sending';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case DELIVERED = 'delivered';
    case UNDELIVERED = 'undelivered';

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => '待发送',
            self::SENDING => '发送中',
            self::SUCCESS => '发送成功',
            self::FAILED => '发送失败',
            self::DELIVERED => '已送达',
            self::UNDELIVERED => '未送达',
        };
    }

    /**
     * @return array<string, self>
     */
    public static function getChoices(): array
    {
        return [
            '待发送' => self::PENDING,
            '发送中' => self::SENDING,
            '发送成功' => self::SUCCESS,
            '发送失败' => self::FAILED,
            '已送达' => self::DELIVERED,
            '未送达' => self::UNDELIVERED,
        ];
    }
}
