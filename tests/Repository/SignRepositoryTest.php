<?php

namespace YunpianSmsBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\Sign;
use YunpianSmsBundle\Repository\SignRepository;

/**
 * @internal
 * @extends AbstractRepositoryTestCase<Sign>
 */
#[CoversClass(SignRepository::class)]
#[RunTestsInSeparateProcesses]
final class SignRepositoryTest extends AbstractRepositoryTestCase
{
    private SignRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(SignRepository::class);
    }

    public function testRepositoryClassExists(): void
    {
        $this->assertInstanceOf(SignRepository::class, $this->repository);
    }

    public function testFindByAccount(): void
    {
        $account = new Account();
        $account->setApiKey('test-sign-account-key');
        $account->setValid(true);

        $otherAccount = new Account();
        $otherAccount->setApiKey('other-sign-account-key');
        $otherAccount->setValid(true);

        $sign1 = new Sign();
        $sign1->setAccount($account);
        $sign1->setSign('测试签名1');
        $sign1->setApplyState('通过');
        $sign1->setValid(true);

        $sign2 = new Sign();
        $sign2->setAccount($account);
        $sign2->setSign('测试签名2');
        $sign2->setApplyState('审核中');
        $sign2->setValid(false);

        $otherSign = new Sign();
        $otherSign->setAccount($otherAccount);
        $otherSign->setSign('其他签名');
        $otherSign->setApplyState('通过');
        $otherSign->setValid(true);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($otherAccount);
        self::getEntityManager()->persist($sign1);
        self::getEntityManager()->persist($sign2);
        self::getEntityManager()->persist($otherSign);
        self::getEntityManager()->flush();

        $signs = $this->repository->findByAccount($account);
        // 移除冗余断言：findByAccount() 的返回类型已声明为 array
        $this->assertCount(2, $signs);

        $foundSigns = [];
        foreach ($signs as $sign) {
            $this->assertSame($account, $sign->getAccount());
            $foundSigns[] = $sign->getSign();
        }

        $this->assertContains('测试签名1', $foundSigns);
        $this->assertContains('测试签名2', $foundSigns);
        $this->assertNotContains('其他签名', $foundSigns);

        self::getEntityManager()->remove($sign1);
        self::getEntityManager()->remove($sign2);
        self::getEntityManager()->remove($otherSign);
        self::getEntityManager()->remove($account);
        self::getEntityManager()->remove($otherAccount);
        self::getEntityManager()->flush();
    }

    public function testFindOneByAccountAndSign(): void
    {
        $account = new Account();
        $account->setApiKey('test-account-sign-key');
        $account->setValid(true);

        $otherAccount = new Account();
        $otherAccount->setApiKey('other-account-sign-key');
        $otherAccount->setValid(true);

        $sign = new Sign();
        $sign->setAccount($account);
        $sign->setSign('唯一测试签名');
        $sign->setApplyState('通过');
        $sign->setValid(true);
        $sign->setRemark('测试备注');

        $otherSign = new Sign();
        $otherSign->setAccount($otherAccount);
        $otherSign->setSign('唯一测试签名');
        $otherSign->setApplyState('通过');
        $otherSign->setValid(true);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($otherAccount);
        self::getEntityManager()->persist($sign);
        self::getEntityManager()->persist($otherSign);
        self::getEntityManager()->flush();

        $foundSign = $this->repository->findOneByAccountAndSign($account, '唯一测试签名');
        $this->assertNotNull($foundSign);
        $this->assertSame($account, $foundSign->getAccount());
        $this->assertSame('唯一测试签名', $foundSign->getSign());
        $this->assertSame('测试备注', $foundSign->getRemark());

        $notFoundByAccount = $this->repository->findOneByAccountAndSign($otherAccount, '不存在的签名');
        $this->assertNull($notFoundByAccount);

        $notFoundBySign = $this->repository->findOneByAccountAndSign($account, '不存在的签名');
        $this->assertNull($notFoundBySign);

        self::getEntityManager()->remove($sign);
        self::getEntityManager()->remove($otherSign);
        self::getEntityManager()->remove($account);
        self::getEntityManager()->remove($otherAccount);
        self::getEntityManager()->flush();
    }

    public function testSaveMethodShouldPersistEntity(): void
    {
        $account = new Account();
        $account->setApiKey('test-save-method-' . uniqid());
        $account->setValid(true);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->flush();

        $sign = new Sign();
        $sign->setAccount($account);
        $sign->setSign('测试保存签名');
        $sign->setApplyState('审核中');
        $sign->setValid(false);
        $sign->setSignId(54321);

        $this->repository->save($sign, true);

        // ID应大于0，改为更有信息量的断言替代无效的 assertNotNull($entity->getId())
        $this->assertGreaterThan(0, $sign->getId());
        $savedSign = self::getEntityManager()->find(Sign::class, $sign->getId());
        $this->assertNotNull($savedSign);
        $this->assertSame('测试保存签名', $savedSign->getSign());
        $this->assertSame('审核中', $savedSign->getApplyState());
        $this->assertFalse($savedSign->isValid());

        self::getEntityManager()->remove($sign);
        self::getEntityManager()->remove($account);
        self::getEntityManager()->flush();
    }

    public function testRemoveMethodShouldDeleteEntity(): void
    {
        $account = new Account();
        $account->setApiKey('test-remove-method-' . uniqid());
        $account->setValid(true);

        $sign = new Sign();
        $sign->setAccount($account);
        $sign->setSign('测试删除签名');
        $sign->setApplyState('通过');
        $sign->setValid(true);
        $sign->setSignId(98765);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($sign);
        self::getEntityManager()->flush();

        $entityId = $sign->getId();
        $this->repository->remove($sign, true);

        $removedSign = self::getEntityManager()->find(Sign::class, $entityId);
        $this->assertNull($removedSign);

        self::getEntityManager()->remove($account);
        self::getEntityManager()->flush();
    }

    public function testFindOneByWithOrderingShouldUseOrderBy(): void
    {
        $account = new Account();
        $account->setApiKey('test-ordering-' . uniqid());
        $account->setValid(true);

        $sign1 = new Sign();
        $sign1->setAccount($account);
        $sign1->setSign('B签名排序');
        $sign1->setApplyState('通过');
        $sign1->setValid(true);
        $sign1->setSignId(100);

        $sign2 = new Sign();
        $sign2->setAccount($account);
        $sign2->setSign('A签名排序');
        $sign2->setApplyState('通过');
        $sign2->setValid(true);
        $sign2->setSignId(200);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($sign1);
        self::getEntityManager()->persist($sign2);
        self::getEntityManager()->flush();

        $foundSign = $this->repository->findOneBy(['account' => $account], ['sign' => 'ASC']);
        $this->assertNotNull($foundSign);
        $this->assertSame('A签名排序', $foundSign->getSign());

        self::getEntityManager()->remove($sign1);
        self::getEntityManager()->remove($sign2);
        self::getEntityManager()->remove($account);
        self::getEntityManager()->flush();
    }

    public function testFindByNullSignIdShouldReturnEntitiesWithNullSignId(): void
    {
        $account = new Account();
        $account->setApiKey('test-null-sign-id-' . uniqid());
        $account->setValid(true);

        $signWithNullSignId = new Sign();
        $signWithNullSignId->setAccount($account);
        $signWithNullSignId->setSign('无SignId签名');
        $signWithNullSignId->setApplyState('审核中');
        $signWithNullSignId->setValid(false);
        // signId is null by default

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($signWithNullSignId);
        self::getEntityManager()->flush();

        $signsWithNullSignId = $this->repository->findBy(['signId' => null]);
        // 移除冗余断言：findBy() 的返回类型已声明为 array
        $this->assertGreaterThanOrEqual(1, count($signsWithNullSignId));

        $found = false;
        foreach ($signsWithNullSignId as $sign) {
            if ($sign->getId() === $signWithNullSignId->getId()) {
                $this->assertNull($sign->getSignId());
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Sign with null signId should be found');

        self::getEntityManager()->remove($signWithNullSignId);
        self::getEntityManager()->remove($account);
        self::getEntityManager()->flush();
    }

    public function testCountNullSignIdFieldsShouldReturnCorrectCount(): void
    {
        $account = new Account();
        $account->setApiKey('test-count-null-sign-id-' . uniqid());
        $account->setValid(true);

        $signWithNullSignId = new Sign();
        $signWithNullSignId->setAccount($account);
        $signWithNullSignId->setSign('计数无SignId签名');
        $signWithNullSignId->setApplyState('拒绝');
        $signWithNullSignId->setValid(false);
        // signId is null by default

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($signWithNullSignId);
        self::getEntityManager()->flush();

        $nullSignIdCount = $this->repository->count(['signId' => null]);
        $this->assertGreaterThanOrEqual(1, $nullSignIdCount);

        self::getEntityManager()->remove($signWithNullSignId);
        self::getEntityManager()->remove($account);
        self::getEntityManager()->flush();
    }

    public function testFindByAssociationWithAccountShouldReturnRelatedEntities(): void
    {
        $account1 = new Account();
        $account1->setApiKey('test-account-association-1-' . uniqid());
        $account1->setValid(true);

        $account2 = new Account();
        $account2->setApiKey('test-account-association-2-' . uniqid());
        $account2->setValid(true);

        $sign1 = new Sign();
        $sign1->setAccount($account1);
        $sign1->setSign('账户1关联签名');
        $sign1->setApplyState('通过');
        $sign1->setValid(true);
        $sign1->setSignId(1001);

        $sign2 = new Sign();
        $sign2->setAccount($account2);
        $sign2->setSign('账户2关联签名');
        $sign2->setApplyState('通过');
        $sign2->setValid(true);
        $sign2->setSignId(2001);

        self::getEntityManager()->persist($account1);
        self::getEntityManager()->persist($account2);
        self::getEntityManager()->persist($sign1);
        self::getEntityManager()->persist($sign2);
        self::getEntityManager()->flush();

        $account1Signs = $this->repository->findBy(['account' => $account1]);
        // 移除冗余断言：findBy() 的返回类型已声明为 array
        $this->assertCount(1, $account1Signs);
        $this->assertSame($account1, $account1Signs[0]->getAccount());
        $this->assertSame('账户1关联签名', $account1Signs[0]->getSign());

        self::getEntityManager()->remove($sign1);
        self::getEntityManager()->remove($sign2);
        self::getEntityManager()->remove($account1);
        self::getEntityManager()->remove($account2);
        self::getEntityManager()->flush();
    }

    public function testCountByAssociationWithAccountShouldReturnCorrectCount(): void
    {
        $account = new Account();
        $account->setApiKey('test-count-account-association-' . uniqid());
        $account->setValid(true);

        $sign1 = new Sign();
        $sign1->setAccount($account);
        $sign1->setSign('计数关联签名1');
        $sign1->setApplyState('通过');
        $sign1->setValid(true);
        $sign1->setSignId(3001);

        $sign2 = new Sign();
        $sign2->setAccount($account);
        $sign2->setSign('计数关联签名2');
        $sign2->setApplyState('审核中');
        $sign2->setValid(false);
        $sign2->setSignId(3002);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($sign1);
        self::getEntityManager()->persist($sign2);
        self::getEntityManager()->flush();

        $accountSignCount = $this->repository->count(['account' => $account]);
        $this->assertSame(2, $accountSignCount);

        self::getEntityManager()->remove($sign1);
        self::getEntityManager()->remove($sign2);
        self::getEntityManager()->remove($account);
        self::getEntityManager()->flush();
    }

    protected function createNewEntity(): object
    {
        $account = new Account();
        $account->setApiKey('test_api_key_' . uniqid());
        $account->setValid(true);
        $account->setRemark('Test Account for Sign');

        $entity = new Sign();
        $entity->setAccount($account);
        $entity->setSign('Test Sign');
        $entity->setApplyState('CHECKING');

        return $entity;
    }

    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
