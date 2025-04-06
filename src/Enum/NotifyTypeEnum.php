<?php

namespace YunpianSmsBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum NotifyTypeEnum: int implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case ALWAYS = 0;
    case ONLY_FAILED = 1;
    case ONLY_SUCCESS = 2;
    case NEVER = 3;

    public function getLabel(): string
    {
        return match($this) {
            self::ALWAYS => '始终通知',
            self::ONLY_FAILED => '仅审核不通过时通知',
            self::ONLY_SUCCESS => '仅审核通过时通知',
            self::NEVER => '不通知',
        };
    }

    public static function getChoices(): array
    {
        return [
            '始终通知' => self::ALWAYS,
            '仅审核不通过时通知' => self::ONLY_FAILED,
            '仅审核通过时通知' => self::ONLY_SUCCESS,
            '不通知' => self::NEVER,
        ];
    }
}
