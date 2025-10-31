<?php

namespace YunpianSmsBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\Template;
use YunpianSmsBundle\Enum\NotifyTypeEnum;
use YunpianSmsBundle\Enum\TemplateTypeEnum;

class TemplateFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public const TEMPLATE_REFERENCE = 'template';

    public function load(ObjectManager $manager): void
    {
        $account = $this->getReference(AccountFixtures::ACCOUNT_REFERENCE, Account::class);

        $template = new Template();
        $template->setAccount($account);
        $template->setTplId('12345678');
        $template->setTitle('测试短信模板');
        $template->setContent('验证码：#code#，请勿泄露给他人。');
        $template->setCheckStatus('VERIFIED');
        $template->setCheckReply('审核通过');
        $template->setNotifyType(NotifyTypeEnum::ALWAYS);
        $template->setTemplateType(TemplateTypeEnum::NOTIFICATION);

        $manager->persist($template);
        $manager->flush();

        $this->addReference(self::TEMPLATE_REFERENCE, $template);
    }

    public function getDependencies(): array
    {
        return [
            AccountFixtures::class,
        ];
    }

    public static function getGroups(): array
    {
        return ['yunpian_sms'];
    }
}
