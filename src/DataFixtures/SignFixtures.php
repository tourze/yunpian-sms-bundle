<?php

namespace YunpianSmsBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\Sign;

class SignFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public const SIGN_REFERENCE = 'sign';

    public function load(ObjectManager $manager): void
    {
        $account = $this->getReference(AccountFixtures::ACCOUNT_REFERENCE, Account::class);

        $sign = new Sign();
        $sign->setAccount($account);
        $sign->setSignId(12345);
        $sign->setSign('测试签名');
        $sign->setApplyState('VERIFIED');
        $sign->setWebsite('https://www.company.com');
        $sign->setNotify(true);

        $manager->persist($sign);
        $manager->flush();

        $this->addReference(self::SIGN_REFERENCE, $sign);
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
