<?php

namespace YunpianSmsBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Repository\AccountRepository;

/**
 * @internal
 * @extends AbstractRepositoryTestCase<Account>
 */
#[CoversClass(AccountRepository::class)]
#[RunTestsInSeparateProcesses]
final class AccountRepositoryTest extends AbstractRepositoryTestCase
{
    private AccountRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(AccountRepository::class);
    }

    public function testFindOneByApiKey(): void
    {
        $account = new Account();
        $account->setApiKey('test-api-key-123');
        $account->setValid(true);
        $account->setRemark('Test Account');

        self::getEntityManager()->persist($account);
        self::getEntityManager()->flush();

        $foundAccount = $this->repository->findOneByApiKey('test-api-key-123');
        $this->assertNotNull($foundAccount);
        $this->assertSame('test-api-key-123', $foundAccount->getApiKey());
        $this->assertSame('Test Account', $foundAccount->getRemark());

        $notFoundAccount = $this->repository->findOneByApiKey('non-existent-key');
        $this->assertNull($notFoundAccount);

        self::getEntityManager()->remove($account);
        self::getEntityManager()->flush();
    }

    public function testFindAllValid(): void
    {
        $validAccount = new Account();
        $validAccount->setApiKey('valid-account-key');
        $validAccount->setValid(true);
        $validAccount->setRemark('Valid Account');

        $invalidAccount = new Account();
        $invalidAccount->setApiKey('invalid-account-key');
        $invalidAccount->setValid(false);
        $invalidAccount->setRemark('Invalid Account');

        self::getEntityManager()->persist($validAccount);
        self::getEntityManager()->persist($invalidAccount);
        self::getEntityManager()->flush();

        $validAccounts = $this->repository->findAllValid();
        // 移除冗余断言：findAllValid() 的返回类型已声明为 array
        $this->assertGreaterThanOrEqual(1, count($validAccounts));

        $foundValidAccount = false;
        $foundInvalidAccount = false;
        foreach ($validAccounts as $account) {
            $this->assertTrue($account->isValid());
            if ('valid-account-key' === $account->getApiKey()) {
                $foundValidAccount = true;
            }
            if ('invalid-account-key' === $account->getApiKey()) {
                $foundInvalidAccount = true;
            }
        }

        $this->assertTrue($foundValidAccount);
        $this->assertFalse($foundInvalidAccount);

        self::getEntityManager()->remove($validAccount);
        self::getEntityManager()->remove($invalidAccount);
        self::getEntityManager()->flush();
    }

    public function testSaveMethodShouldPersistEntity(): void
    {
        $account = new Account();
        $account->setApiKey('test-save-' . uniqid());
        $account->setValid(true);
        $account->setRemark('Test Save Account');

        $this->repository->save($account);

        // ID应大于0，改为更有信息量的断言替代无效的 assertNotNull($entity->getId())
        $this->assertGreaterThan(0, $account->getId());
        $foundAccount = $this->repository->find($account->getId());
        $this->assertNotNull($foundAccount);
        $this->assertSame($account->getApiKey(), $foundAccount->getApiKey());

        self::getEntityManager()->remove($account);
        self::getEntityManager()->flush();
    }

    public function testRemoveMethodShouldDeleteEntity(): void
    {
        $account = new Account();
        $account->setApiKey('test-remove-' . uniqid());
        $account->setValid(true);
        $account->setRemark('Test Remove Account');

        self::getEntityManager()->persist($account);
        self::getEntityManager()->flush();
        $accountId = $account->getId();

        $this->repository->remove($account);

        $foundAccount = $this->repository->find($accountId);
        $this->assertNull($foundAccount);
    }

    public function testFindOneByWithOrderingShouldUseOrderBy(): void
    {
        $account1 = new Account();
        $account1->setApiKey('test-order-find-one-b');
        $account1->setValid(true);
        $account1->setRemark('Order Test B');

        $account2 = new Account();
        $account2->setApiKey('test-order-find-one-a');
        $account2->setValid(true);
        $account2->setRemark('Order Test A');

        self::getEntityManager()->persist($account1);
        self::getEntityManager()->persist($account2);
        self::getEntityManager()->flush();

        $foundAccount = $this->repository->findOneBy(['valid' => true], ['apiKey' => 'ASC']);
        $this->assertNotNull($foundAccount);

        self::getEntityManager()->remove($account1);
        self::getEntityManager()->remove($account2);
        self::getEntityManager()->flush();
    }

    public function testFindByNullValidShouldReturnAccountsWithNullValid(): void
    {
        $account = new Account();
        $account->setApiKey('test-null-valid-' . uniqid());
        $account->setValid(null);
        $account->setRemark('Null Valid Account');

        self::getEntityManager()->persist($account);
        self::getEntityManager()->flush();

        $nullValidAccounts = $this->repository->findBy(['valid' => null]);
        // 移除冗余断言：findBy() 的返回类型已声明为 array
        $this->assertGreaterThanOrEqual(1, count($nullValidAccounts));

        $foundAccount = false;
        foreach ($nullValidAccounts as $nullAccount) {
            if ($nullAccount->getApiKey() === $account->getApiKey()) {
                $foundAccount = true;
                $this->assertNull($nullAccount->isValid());
            }
        }
        $this->assertTrue($foundAccount);

        self::getEntityManager()->remove($account);
        self::getEntityManager()->flush();
    }

    public function testFindByNullRemarkShouldReturnAccountsWithNullRemark(): void
    {
        $account = new Account();
        $account->setApiKey('test-null-remark-' . uniqid());
        $account->setValid(true);
        $account->setRemark(null);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->flush();

        $nullRemarkAccounts = $this->repository->findBy(['remark' => null]);
        // 移除冗余断言：findBy() 的返回类型已声明为 array
        $this->assertGreaterThanOrEqual(1, count($nullRemarkAccounts));

        $foundAccount = false;
        foreach ($nullRemarkAccounts as $nullAccount) {
            if ($nullAccount->getApiKey() === $account->getApiKey()) {
                $foundAccount = true;
                $this->assertNull($nullAccount->getRemark());
            }
        }
        $this->assertTrue($foundAccount);

        self::getEntityManager()->remove($account);
        self::getEntityManager()->flush();
    }

    public function testCountNullValidFieldsShouldReturnCorrectCount(): void
    {
        $account1 = new Account();
        $account1->setApiKey('test-count-null-valid-1-' . uniqid());
        $account1->setValid(null);
        $account1->setRemark('Null Valid Account 1');

        $account2 = new Account();
        $account2->setApiKey('test-count-null-valid-2-' . uniqid());
        $account2->setValid(null);
        $account2->setRemark('Null Valid Account 2');

        self::getEntityManager()->persist($account1);
        self::getEntityManager()->persist($account2);
        self::getEntityManager()->flush();

        $nullValidCount = $this->repository->count(['valid' => null]);
        $this->assertGreaterThanOrEqual(2, $nullValidCount);

        self::getEntityManager()->remove($account1);
        self::getEntityManager()->remove($account2);
        self::getEntityManager()->flush();
    }

    public function testCountNullRemarkFieldsShouldReturnCorrectCount(): void
    {
        $account1 = new Account();
        $account1->setApiKey('test-count-null-remark-1-' . uniqid());
        $account1->setValid(true);
        $account1->setRemark(null);

        $account2 = new Account();
        $account2->setApiKey('test-count-null-remark-2-' . uniqid());
        $account2->setValid(true);
        $account2->setRemark(null);

        self::getEntityManager()->persist($account1);
        self::getEntityManager()->persist($account2);
        self::getEntityManager()->flush();

        $nullRemarkCount = $this->repository->count(['remark' => null]);
        $this->assertGreaterThanOrEqual(2, $nullRemarkCount);

        self::getEntityManager()->remove($account1);
        self::getEntityManager()->remove($account2);
        self::getEntityManager()->flush();
    }

    protected function createNewEntity(): object
    {
        $entity = new Account();
        $entity->setApiKey('test_api_key_' . uniqid());
        $entity->setValid(true);
        $entity->setRemark('Test Account ' . uniqid());

        return $entity;
    }

    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
