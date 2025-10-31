<?php

namespace YunpianSmsBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\SendLog;
use YunpianSmsBundle\Enum\SendStatusEnum;

class SendLogFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $account = $this->getReference(AccountFixtures::ACCOUNT_REFERENCE, Account::class);

        $sendLog = new SendLog();
        $sendLog->setAccount($account);
        $sendLog->setMobile('13800138000');
        $sendLog->setContent('测试短信内容');
        $sendLog->setUid('test_uid_' . time());
        $sendLog->setSid('test_sid_' . time());
        $sendLog->setCount(1);
        $sendLog->setFee('0.050');
        $sendLog->setStatus(SendStatusEnum::SUCCESS);
        $sendLog->setStatusMsg('发送成功');

        $manager->persist($sendLog);
        $manager->flush();
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
