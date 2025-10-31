<?php

namespace YunpianSmsBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\SendLog;
use YunpianSmsBundle\Enum\SendStatusEnum;
use YunpianSmsBundle\Repository\SendLogRepository;

/**
 * @internal
 */
#[CoversClass(SendLogRepository::class)]
#[RunTestsInSeparateProcesses]
final class SendLogRepositoryTest extends AbstractRepositoryTestCase
{
    private SendLogRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(SendLogRepository::class);
    }

    public function testRepositoryClassExists(): void
    {
        $this->assertInstanceOf(SendLogRepository::class, $this->repository);
    }

    public function testFindPendingStatus(): void
    {
        $account = new Account();
        $account->setApiKey('test-send-log-key');
        $account->setValid(true);

        $pendingLog1 = new SendLog();
        $pendingLog1->setAccount($account);
        $pendingLog1->setMobile('13800138001');
        $pendingLog1->setContent('Test pending message 1');
        $pendingLog1->setStatus(SendStatusEnum::PENDING);
        $pendingLog1->setSid('test-sid-1');

        $pendingLog2 = new SendLog();
        $pendingLog2->setAccount($account);
        $pendingLog2->setMobile('13800138002');
        $pendingLog2->setContent('Test pending message 2');
        $pendingLog2->setStatus(SendStatusEnum::PENDING);
        $pendingLog2->setSid('test-sid-2');

        $pendingLogWithoutSid = new SendLog();
        $pendingLogWithoutSid->setAccount($account);
        $pendingLogWithoutSid->setMobile('13800138003');
        $pendingLogWithoutSid->setContent('Test pending message without sid');
        $pendingLogWithoutSid->setStatus(SendStatusEnum::PENDING);

        $successLog = new SendLog();
        $successLog->setAccount($account);
        $successLog->setMobile('13800138004');
        $successLog->setContent('Test success message');
        $successLog->setStatus(SendStatusEnum::SUCCESS);
        $successLog->setSid('test-sid-success');

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($pendingLog1);
        self::getEntityManager()->persist($pendingLog2);
        self::getEntityManager()->persist($pendingLogWithoutSid);
        self::getEntityManager()->persist($successLog);
        self::getEntityManager()->flush();

        $pendingLogs = $this->repository->findPendingStatus(10);
        $this->assertIsArray($pendingLogs);
        $this->assertGreaterThanOrEqual(2, count($pendingLogs));
        $this->assertLessThanOrEqual(10, count($pendingLogs));

        foreach ($pendingLogs as $log) {
            $this->assertSame(SendStatusEnum::PENDING, $log->getStatus());
            $this->assertNotNull($log->getSid());
        }

        $pendingLogsLimited = $this->repository->findPendingStatus(1);
        $this->assertCount(1, $pendingLogsLimited);

        self::getEntityManager()->remove($pendingLog1);
        self::getEntityManager()->remove($pendingLog2);
        self::getEntityManager()->remove($pendingLogWithoutSid);
        self::getEntityManager()->remove($successLog);
        self::getEntityManager()->remove($account);
        self::getEntityManager()->flush();
    }

    public function testFindOneBySid(): void
    {
        $account = new Account();
        $account->setApiKey('test-find-by-sid-key');
        $account->setValid(true);

        $sendLog = new SendLog();
        $sendLog->setAccount($account);
        $sendLog->setMobile('13800138005');
        $sendLog->setContent('Test message with sid');
        $sendLog->setStatus(SendStatusEnum::SUCCESS);
        $sendLog->setSid('unique-test-sid-123');

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($sendLog);
        self::getEntityManager()->flush();

        $foundLog = $this->repository->findOneBySid('unique-test-sid-123');
        $this->assertNotNull($foundLog);
        $this->assertSame('unique-test-sid-123', $foundLog->getSid());
        $this->assertSame('13800138005', $foundLog->getMobile());
        $this->assertSame('Test message with sid', $foundLog->getContent());

        $notFoundLog = $this->repository->findOneBySid('non-existent-sid');
        $this->assertNull($notFoundLog);

        self::getEntityManager()->remove($sendLog);
        self::getEntityManager()->remove($account);
        self::getEntityManager()->flush();
    }

    public function testFindLastSendTime(): void
    {
        $account = new Account();
        $account->setApiKey('test-last-send-time-' . time() . '-' . rand(1000, 9999));
        $account->setValid(true);

        $oldLog = new SendLog();
        $oldLog->setAccount($account);
        $oldLog->setMobile('13800138006');
        $oldLog->setContent('Old message');
        $oldLog->setStatus(SendStatusEnum::SUCCESS);

        $newLog = new SendLog();
        $newLog->setAccount($account);
        $newLog->setMobile('13800138007');
        $newLog->setContent('New message');
        $newLog->setStatus(SendStatusEnum::SUCCESS);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($oldLog);
        self::getEntityManager()->flush();

        sleep(1);

        self::getEntityManager()->persist($newLog);
        self::getEntityManager()->flush();

        $lastSendTime = $this->repository->findLastSendTime();
        $this->assertNotNull($lastSendTime);
        $this->assertInstanceOf(\DateTimeInterface::class, $lastSendTime);

        self::getEntityManager()->remove($oldLog);
        self::getEntityManager()->remove($newLog);
        self::getEntityManager()->remove($account);
        self::getEntityManager()->flush();

        $noSendTime = $this->repository->findLastSendTime();
        // 检查是否还有其他记录（可能来自其他测试）
        $allLogs = self::getEntityManager()->getRepository(SendLog::class)->findAll();
        if (count($allLogs) > 0) {
            // 如果有其他记录，我们期望返回最后一条的时间
            $this->assertNotNull($noSendTime);
        } else {
            // 如果没有记录，期望返回 null
            $this->assertNull($noSendTime);
        }
    }

    public function testSaveMethodShouldPersistEntity(): void
    {
        $account = new Account();
        $account->setApiKey('test-save-method-' . uniqid());
        $account->setValid(true);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->flush();

        $sendLog = new SendLog();
        $sendLog->setAccount($account);
        $sendLog->setMobile('13800138001');
        $sendLog->setContent('Test save method');
        $sendLog->setStatus(SendStatusEnum::PENDING);
        $sendLog->setSid('test-save-method-sid');

        $this->repository->save($sendLog, true);

        $this->assertNotNull($sendLog->getId());
        $savedLog = self::getEntityManager()->find(SendLog::class, $sendLog->getId());
        $this->assertNotNull($savedLog);
        $this->assertSame('13800138001', $savedLog->getMobile());
        $this->assertSame('Test save method', $savedLog->getContent());

        self::getEntityManager()->remove($sendLog);
        self::getEntityManager()->remove($account);
        self::getEntityManager()->flush();
    }

    public function testRemoveMethodShouldDeleteEntity(): void
    {
        $account = new Account();
        $account->setApiKey('test-remove-method-' . uniqid());
        $account->setValid(true);

        $sendLog = new SendLog();
        $sendLog->setAccount($account);
        $sendLog->setMobile('13800138002');
        $sendLog->setContent('Test remove method');
        $sendLog->setStatus(SendStatusEnum::SUCCESS);
        $sendLog->setSid('test-remove-method-sid');

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($sendLog);
        self::getEntityManager()->flush();

        $entityId = $sendLog->getId();
        $this->repository->remove($sendLog, true);

        $removedLog = self::getEntityManager()->find(SendLog::class, $entityId);
        $this->assertNull($removedLog);

        self::getEntityManager()->remove($account);
        self::getEntityManager()->flush();
    }

    public function testFindOneByWithOrderingShouldUseOrderBy(): void
    {
        $account = new Account();
        $account->setApiKey('test-ordering-' . uniqid());
        $account->setValid(true);

        $sendLog1 = new SendLog();
        $sendLog1->setAccount($account);
        $sendLog1->setMobile('13800138003');
        $sendLog1->setContent('First message');
        $sendLog1->setStatus(SendStatusEnum::SUCCESS);
        $sendLog1->setSid('test-ordering-sid-1');

        $sendLog2 = new SendLog();
        $sendLog2->setAccount($account);
        $sendLog2->setMobile('13800138004');
        $sendLog2->setContent('Second message');
        $sendLog2->setStatus(SendStatusEnum::SUCCESS);
        $sendLog2->setSid('test-ordering-sid-2');

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($sendLog1);
        self::getEntityManager()->flush();

        sleep(1);
        self::getEntityManager()->persist($sendLog2);
        self::getEntityManager()->flush();

        $foundLog = $this->repository->findOneBy(['account' => $account], ['id' => 'DESC']);
        $this->assertNotNull($foundLog);
        $this->assertSame('Second message', $foundLog->getContent());

        self::getEntityManager()->remove($sendLog1);
        self::getEntityManager()->remove($sendLog2);
        self::getEntityManager()->remove($account);
        self::getEntityManager()->flush();
    }

    public function testFindByNullTemplateShouldReturnEntitiesWithNullTemplate(): void
    {
        $account = new Account();
        $account->setApiKey('test-null-template-' . uniqid());
        $account->setValid(true);

        $sendLogWithNullTemplate = new SendLog();
        $sendLogWithNullTemplate->setAccount($account);
        $sendLogWithNullTemplate->setMobile('13800138005');
        $sendLogWithNullTemplate->setContent('No template message');
        $sendLogWithNullTemplate->setStatus(SendStatusEnum::SUCCESS);
        $sendLogWithNullTemplate->setSid('test-null-template-sid');

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($sendLogWithNullTemplate);
        self::getEntityManager()->flush();

        $logsWithNullTemplate = $this->repository->findBy(['template' => null]);
        $this->assertIsArray($logsWithNullTemplate);
        $this->assertGreaterThanOrEqual(1, count($logsWithNullTemplate));

        $found = false;
        foreach ($logsWithNullTemplate as $log) {
            if ($log->getId() === $sendLogWithNullTemplate->getId()) {
                $this->assertNull($log->getTemplate());
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'SendLog with null template should be found');

        self::getEntityManager()->remove($sendLogWithNullTemplate);
        self::getEntityManager()->remove($account);
        self::getEntityManager()->flush();
    }

    public function testCountNullTemplateFieldsShouldReturnCorrectCount(): void
    {
        $account = new Account();
        $account->setApiKey('test-count-null-template-' . uniqid());
        $account->setValid(true);

        $sendLogWithNullTemplate = new SendLog();
        $sendLogWithNullTemplate->setAccount($account);
        $sendLogWithNullTemplate->setMobile('13800138006');
        $sendLogWithNullTemplate->setContent('Count null template message');
        $sendLogWithNullTemplate->setStatus(SendStatusEnum::SUCCESS);
        $sendLogWithNullTemplate->setSid('test-count-null-template-sid');

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($sendLogWithNullTemplate);
        self::getEntityManager()->flush();

        $nullTemplateCount = $this->repository->count(['template' => null]);
        $this->assertGreaterThanOrEqual(1, $nullTemplateCount);

        self::getEntityManager()->remove($sendLogWithNullTemplate);
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

        $sendLog1 = new SendLog();
        $sendLog1->setAccount($account1);
        $sendLog1->setMobile('13800138007');
        $sendLog1->setContent('Account 1 message');
        $sendLog1->setStatus(SendStatusEnum::SUCCESS);
        $sendLog1->setSid('test-account-1-sid');

        $sendLog2 = new SendLog();
        $sendLog2->setAccount($account2);
        $sendLog2->setMobile('13800138008');
        $sendLog2->setContent('Account 2 message');
        $sendLog2->setStatus(SendStatusEnum::SUCCESS);
        $sendLog2->setSid('test-account-2-sid');

        self::getEntityManager()->persist($account1);
        self::getEntityManager()->persist($account2);
        self::getEntityManager()->persist($sendLog1);
        self::getEntityManager()->persist($sendLog2);
        self::getEntityManager()->flush();

        $account1Logs = $this->repository->findBy(['account' => $account1]);
        $this->assertIsArray($account1Logs);
        $this->assertCount(1, $account1Logs);
        $this->assertSame($account1, $account1Logs[0]->getAccount());
        $this->assertSame('Account 1 message', $account1Logs[0]->getContent());

        self::getEntityManager()->remove($sendLog1);
        self::getEntityManager()->remove($sendLog2);
        self::getEntityManager()->remove($account1);
        self::getEntityManager()->remove($account2);
        self::getEntityManager()->flush();
    }

    public function testCountByAssociationWithAccountShouldReturnCorrectCount(): void
    {
        $account = new Account();
        $account->setApiKey('test-count-account-association-' . uniqid());
        $account->setValid(true);

        $sendLog1 = new SendLog();
        $sendLog1->setAccount($account);
        $sendLog1->setMobile('13800138009');
        $sendLog1->setContent('Count association message 1');
        $sendLog1->setStatus(SendStatusEnum::SUCCESS);
        $sendLog1->setSid('test-count-association-sid-1');

        $sendLog2 = new SendLog();
        $sendLog2->setAccount($account);
        $sendLog2->setMobile('13800138010');
        $sendLog2->setContent('Count association message 2');
        $sendLog2->setStatus(SendStatusEnum::SUCCESS);
        $sendLog2->setSid('test-count-association-sid-2');

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($sendLog1);
        self::getEntityManager()->persist($sendLog2);
        self::getEntityManager()->flush();

        $accountLogCount = $this->repository->count(['account' => $account]);
        $this->assertSame(2, $accountLogCount);

        self::getEntityManager()->remove($sendLog1);
        self::getEntityManager()->remove($sendLog2);
        self::getEntityManager()->remove($account);
        self::getEntityManager()->flush();
    }

    protected function createNewEntity(): object
    {
        $account = new Account();
        $account->setApiKey('test_api_key_' . uniqid());
        $account->setValid(true);
        $account->setRemark('Test Account for SendLog');

        $entity = new SendLog();
        $entity->setAccount($account);
        $entity->setMobile('13800138000');
        $entity->setContent('Test message');
        $entity->setCount(1);
        $entity->setFee('0.1');

        return $entity;
    }

    protected function getRepository(): SendLogRepository
    {
        return $this->repository;
    }
}
