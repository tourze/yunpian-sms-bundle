<?php

namespace YunpianSmsBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use YunpianSmsBundle\Entity\Account;

class AccountFixtures extends Fixture implements FixtureGroupInterface
{
    public const ACCOUNT_REFERENCE = 'account';

    public function load(ObjectManager $manager): void
    {
        $account = new Account();
        $account->setApiKey('test_api_key_' . bin2hex(random_bytes(16)));
        $account->setValid(true);
        $account->setRemark('测试账号');

        $manager->persist($account);
        $manager->flush();

        $this->addReference(self::ACCOUNT_REFERENCE, $account);
    }

    public static function getGroups(): array
    {
        return ['yunpian_sms'];
    }
}
