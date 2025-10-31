<?php

namespace YunpianSmsBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\DailyConsumption;

class DailyConsumptionFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $account = $this->getReference(AccountFixtures::ACCOUNT_REFERENCE, Account::class);

        $consumption = new DailyConsumption();
        $consumption->setAccount($account);
        $consumption->setDate(new \DateTimeImmutable('yesterday'));
        $consumption->setTotalCount(100);
        $consumption->setTotalFee('5.000');
        $consumption->setTotalSuccessCount(95);
        $consumption->setTotalFailedCount(3);
        $consumption->setTotalUnknownCount(2);
        $consumption->setItems([
            ['type' => '短信', 'count' => 100, 'fee' => '5.000'],
        ]);

        $manager->persist($consumption);
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
