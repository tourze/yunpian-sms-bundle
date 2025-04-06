<?php

namespace YunpianSmsBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum TemplateTypeEnum: int implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case NOTIFICATION = 0;
    case VERIFICATION = 1;

    public function getLabel(): string
    {
        return match($this) {
            self::NOTIFICATION => '通知类模板',
            self::VERIFICATION => '验证码类模板',
        };
    }

    public static function getChoices(): array
    {
        return [
            '通知类模板' => self::NOTIFICATION,
            '验证码类模板' => self::VERIFICATION,
        ];
    }

    public function isVerification(): bool
    {
        return $this === self::VERIFICATION;
    }

    public function isNotification(): bool
    {
        return $this === self::NOTIFICATION;
    }
}
