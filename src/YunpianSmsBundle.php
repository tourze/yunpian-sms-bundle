<?php

namespace YunpianSmsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[AsPermission(title: '云片短信')]
class YunpianSmsBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            \AmisBundle\AmisBundle::class => ['all' => true],
            \Tourze\Symfony\CronJob\CronJobBundle::class => ['all' => true],
        ];
    }
}
