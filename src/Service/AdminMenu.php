<?php

namespace YunpianSmsBundle\Service;

use Knp\Menu\ItemInterface;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\DailyConsumption;
use YunpianSmsBundle\Entity\SendLog;
use YunpianSmsBundle\Entity\Sign;
use YunpianSmsBundle\Entity\Template;

/**
 * 云片短信菜单服务
 */
class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private readonly LinkGeneratorInterface $linkGenerator,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (null === $item->getChild('云片短信')) {
            $item->addChild('云片短信');
        }

        $yunpianMenu = $item->getChild('云片短信');
        if (null === $yunpianMenu) {
            return;
        }

        // 账号管理菜单
        $yunpianMenu->addChild('账号管理')
            ->setUri($this->linkGenerator->getCurdListPage(Account::class))
            ->setAttribute('icon', 'fas fa-key')
        ;

        // 短信模板菜单
        $yunpianMenu->addChild('短信模板')
            ->setUri($this->linkGenerator->getCurdListPage(Template::class))
            ->setAttribute('icon', 'fas fa-file-alt')
        ;

        // 短信签名菜单
        $yunpianMenu->addChild('短信签名')
            ->setUri($this->linkGenerator->getCurdListPage(Sign::class))
            ->setAttribute('icon', 'fas fa-signature')
        ;

        // 发送记录菜单
        $yunpianMenu->addChild('发送记录')
            ->setUri($this->linkGenerator->getCurdListPage(SendLog::class))
            ->setAttribute('icon', 'fas fa-paper-plane')
        ;

        // 日消费统计菜单
        $yunpianMenu->addChild('日消费统计')
            ->setUri($this->linkGenerator->getCurdListPage(DailyConsumption::class))
            ->setAttribute('icon', 'fas fa-chart-line')
        ;
    }
}
