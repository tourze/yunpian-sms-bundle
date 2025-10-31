<?php

namespace YunpianSmsBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\Template;
use YunpianSmsBundle\Enum\TemplateTypeEnum;
use YunpianSmsBundle\Repository\TemplateRepository;

/**
 * @internal
 */
#[CoversClass(TemplateRepository::class)]
#[RunTestsInSeparateProcesses]
final class TemplateRepositoryTest extends AbstractRepositoryTestCase
{
    private TemplateRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(TemplateRepository::class);
    }

    public function testRepositoryClassExists(): void
    {
        $this->assertInstanceOf(TemplateRepository::class, $this->repository);
    }

    public function testFindByAccount(): void
    {
        $account = new Account();
        $account->setApiKey('test-template-account-key');
        $account->setValid(true);

        $otherAccount = new Account();
        $otherAccount->setApiKey('other-template-account-key');
        $otherAccount->setValid(true);

        $template1 = new Template();
        $template1->setAccount($account);
        $template1->setTplId('123456');
        $template1->setTitle('测试模板1');
        $template1->setContent('您的验证码是#code#');
        $template1->setCheckStatus('通过');
        $template1->setValid(true);

        $template2 = new Template();
        $template2->setAccount($account);
        $template2->setTplId('789012');
        $template2->setTitle('测试模板2');
        $template2->setContent('欢迎注册#name#');
        $template2->setCheckStatus('审核中');
        $template2->setValid(false);

        $otherTemplate = new Template();
        $otherTemplate->setAccount($otherAccount);
        $otherTemplate->setTplId('345678');
        $otherTemplate->setTitle('其他模板');
        $otherTemplate->setContent('其他内容');
        $otherTemplate->setCheckStatus('通过');
        $otherTemplate->setValid(true);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($otherAccount);
        self::getEntityManager()->persist($template1);
        self::getEntityManager()->persist($template2);
        self::getEntityManager()->persist($otherTemplate);
        self::getEntityManager()->flush();

        $templates = $this->repository->findByAccount($account);
        $this->assertIsArray($templates);
        $this->assertCount(2, $templates);

        $foundTitles = [];
        foreach ($templates as $template) {
            $this->assertSame($account, $template->getAccount());
            $foundTitles[] = $template->getTitle();
        }

        $this->assertContains('测试模板1', $foundTitles);
        $this->assertContains('测试模板2', $foundTitles);
        $this->assertNotContains('其他模板', $foundTitles);

        self::getEntityManager()->remove($template1);
        self::getEntityManager()->remove($template2);
        self::getEntityManager()->remove($otherTemplate);
        self::getEntityManager()->remove($account);
        self::getEntityManager()->remove($otherAccount);
        self::getEntityManager()->flush();
    }

    public function testFindOneByAccountAndTplId(): void
    {
        $account = new Account();
        $account->setApiKey('test-account-tpl-key');
        $account->setValid(true);

        $otherAccount = new Account();
        $otherAccount->setApiKey('other-account-tpl-key');
        $otherAccount->setValid(true);

        $template = new Template();
        $template->setAccount($account);
        $template->setTplId('999888777');
        $template->setTitle('唯一测试模板');
        $template->setContent('这是一个唯一的测试模板内容');
        $template->setCheckStatus('通过');
        $template->setValid(true);

        $otherTemplate = new Template();
        $otherTemplate->setAccount($otherAccount);
        $otherTemplate->setTplId('999888666');
        $otherTemplate->setTitle('其他账户的模板');
        $otherTemplate->setContent('不同账户的模板');
        $otherTemplate->setCheckStatus('通过');
        $otherTemplate->setValid(true);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($otherAccount);
        self::getEntityManager()->persist($template);
        self::getEntityManager()->persist($otherTemplate);
        self::getEntityManager()->flush();

        $foundTemplate = $this->repository->findOneByAccountAndTplId($account, '999888777');
        $this->assertNotNull($foundTemplate);
        $this->assertSame($account, $foundTemplate->getAccount());
        $this->assertSame('999888777', $foundTemplate->getTplId());
        $this->assertSame('唯一测试模板', $foundTemplate->getTitle());
        $this->assertSame('这是一个唯一的测试模板内容', $foundTemplate->getContent());

        $notFoundByAccount = $this->repository->findOneByAccountAndTplId($otherAccount, '不存在的ID');
        $this->assertNull($notFoundByAccount);

        $notFoundByTplId = $this->repository->findOneByAccountAndTplId($account, '不存在的ID');
        $this->assertNull($notFoundByTplId);

        self::getEntityManager()->remove($template);
        self::getEntityManager()->remove($otherTemplate);
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

        $template = new Template();
        $template->setAccount($account);
        $template->setTplId('888777666');
        $template->setTitle('测试保存模板');
        $template->setContent('测试保存模板内容#code#');
        $template->setCheckStatus('审核中');
        $template->setValid(false);

        $this->repository->save($template, true);

        $this->assertNotNull($template->getId());
        $savedTemplate = self::getEntityManager()->find(Template::class, $template->getId());
        $this->assertNotNull($savedTemplate);
        $this->assertSame('888777666', $savedTemplate->getTplId());
        $this->assertSame('测试保存模板', $savedTemplate->getTitle());
        $this->assertSame('审核中', $savedTemplate->getCheckStatus());
        $this->assertFalse($savedTemplate->isValid());

        self::getEntityManager()->remove($template);
        self::getEntityManager()->remove($account);
        self::getEntityManager()->flush();
    }

    public function testRemoveMethodShouldDeleteEntity(): void
    {
        $account = new Account();
        $account->setApiKey('test-remove-method-' . uniqid());
        $account->setValid(true);

        $template = new Template();
        $template->setAccount($account);
        $template->setTplId('777666555');
        $template->setTitle('测试删除模板');
        $template->setContent('测试删除模板内容#code#');
        $template->setCheckStatus('通过');
        $template->setValid(true);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($template);
        self::getEntityManager()->flush();

        $entityId = $template->getId();
        $this->repository->remove($template, true);

        $removedTemplate = self::getEntityManager()->find(Template::class, $entityId);
        $this->assertNull($removedTemplate);

        self::getEntityManager()->remove($account);
        self::getEntityManager()->flush();
    }

    public function testFindOneByWithOrderingShouldUseOrderBy(): void
    {
        $account = new Account();
        $account->setApiKey('test-ordering-' . uniqid());
        $account->setValid(true);

        $template1 = new Template();
        $template1->setAccount($account);
        $template1->setTplId('100001');
        $template1->setTitle('B模板排序');
        $template1->setContent('B模板内容#code#');
        $template1->setCheckStatus('通过');
        $template1->setValid(true);

        $template2 = new Template();
        $template2->setAccount($account);
        $template2->setTplId('100002');
        $template2->setTitle('A模板排序');
        $template2->setContent('A模板内容#code#');
        $template2->setCheckStatus('通过');
        $template2->setValid(true);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($template1);
        self::getEntityManager()->persist($template2);
        self::getEntityManager()->flush();

        $foundTemplate = $this->repository->findOneBy(['account' => $account], ['title' => 'ASC']);
        $this->assertNotNull($foundTemplate);
        $this->assertSame('A模板排序', $foundTemplate->getTitle());

        self::getEntityManager()->remove($template1);
        self::getEntityManager()->remove($template2);
        self::getEntityManager()->remove($account);
        self::getEntityManager()->flush();
    }

    public function testFindByNullCheckReplyShouldReturnEntitiesWithNullCheckReply(): void
    {
        $account = new Account();
        $account->setApiKey('test-null-check-reply-' . uniqid());
        $account->setValid(true);

        $templateWithNullCheckReply = new Template();
        $templateWithNullCheckReply->setAccount($account);
        $templateWithNullCheckReply->setTplId('200001');
        $templateWithNullCheckReply->setTitle('无审核说明模板');
        $templateWithNullCheckReply->setContent('无审核说明模板内容#code#');
        $templateWithNullCheckReply->setCheckStatus('审核中');
        $templateWithNullCheckReply->setValid(false);
        // checkReply is null by default

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($templateWithNullCheckReply);
        self::getEntityManager()->flush();

        $templatesWithNullCheckReply = $this->repository->findBy(['checkReply' => null]);
        $this->assertIsArray($templatesWithNullCheckReply);
        $this->assertGreaterThanOrEqual(1, count($templatesWithNullCheckReply));

        $found = false;
        foreach ($templatesWithNullCheckReply as $template) {
            if ($template->getId() === $templateWithNullCheckReply->getId()) {
                $this->assertNull($template->getCheckReply());
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Template with null checkReply should be found');

        self::getEntityManager()->remove($templateWithNullCheckReply);
        self::getEntityManager()->remove($account);
        self::getEntityManager()->flush();
    }

    public function testCountNullCheckReplyFieldsShouldReturnCorrectCount(): void
    {
        $account = new Account();
        $account->setApiKey('test-count-null-check-reply-' . uniqid());
        $account->setValid(true);

        $templateWithNullCheckReply = new Template();
        $templateWithNullCheckReply->setAccount($account);
        $templateWithNullCheckReply->setTplId('300001');
        $templateWithNullCheckReply->setTitle('计数无审核说明模板');
        $templateWithNullCheckReply->setContent('计数无审核说明模板内容#code#');
        $templateWithNullCheckReply->setCheckStatus('拒绝');
        $templateWithNullCheckReply->setValid(false);
        // checkReply is null by default

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($templateWithNullCheckReply);
        self::getEntityManager()->flush();

        $nullCheckReplyCount = $this->repository->count(['checkReply' => null]);
        $this->assertGreaterThanOrEqual(1, $nullCheckReplyCount);

        self::getEntityManager()->remove($templateWithNullCheckReply);
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

        $template1 = new Template();
        $template1->setAccount($account1);
        $template1->setTplId('400001');
        $template1->setTitle('账户1关联模板');
        $template1->setContent('账户1关联模板内容#code#');
        $template1->setCheckStatus('通过');
        $template1->setValid(true);

        $template2 = new Template();
        $template2->setAccount($account2);
        $template2->setTplId('400002');
        $template2->setTitle('账户2关联模板');
        $template2->setContent('账户2关联模板内容#code#');
        $template2->setCheckStatus('通过');
        $template2->setValid(true);

        self::getEntityManager()->persist($account1);
        self::getEntityManager()->persist($account2);
        self::getEntityManager()->persist($template1);
        self::getEntityManager()->persist($template2);
        self::getEntityManager()->flush();

        $account1Templates = $this->repository->findBy(['account' => $account1]);
        $this->assertIsArray($account1Templates);
        $this->assertCount(1, $account1Templates);
        $this->assertSame($account1, $account1Templates[0]->getAccount());
        $this->assertSame('账户1关联模板', $account1Templates[0]->getTitle());

        self::getEntityManager()->remove($template1);
        self::getEntityManager()->remove($template2);
        self::getEntityManager()->remove($account1);
        self::getEntityManager()->remove($account2);
        self::getEntityManager()->flush();
    }

    public function testCountByAssociationWithAccountShouldReturnCorrectCount(): void
    {
        $account = new Account();
        $account->setApiKey('test-count-account-association-' . uniqid());
        $account->setValid(true);

        $template1 = new Template();
        $template1->setAccount($account);
        $template1->setTplId('500001');
        $template1->setTitle('计数关联模板1');
        $template1->setContent('计数关联模板内容1#code#');
        $template1->setCheckStatus('通过');
        $template1->setValid(true);

        $template2 = new Template();
        $template2->setAccount($account);
        $template2->setTplId('500002');
        $template2->setTitle('计数关联模板2');
        $template2->setContent('计数关联模板内容2#code#');
        $template2->setCheckStatus('审核中');
        $template2->setValid(false);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($template1);
        self::getEntityManager()->persist($template2);
        self::getEntityManager()->flush();

        $accountTemplateCount = $this->repository->count(['account' => $account]);
        $this->assertSame(2, $accountTemplateCount);

        self::getEntityManager()->remove($template1);
        self::getEntityManager()->remove($template2);
        self::getEntityManager()->remove($account);
        self::getEntityManager()->flush();
    }

    protected function createNewEntity(): object
    {
        $account = new Account();
        $account->setApiKey('test_api_key_' . uniqid());
        $account->setValid(true);
        $account->setRemark('Test Account for Template');

        $entity = new Template();
        /** @var int $tplIdSeed */
        static $tplIdSeed = 1_000_000_000;

        $entity->setAccount($account);
        $entity->setTplId((string) $tplIdSeed++);
        $entity->setTitle('Test Template Title');
        $entity->setContent('Test template content');
        $entity->setCheckStatus('CHECKING');
        $entity->setTemplateType(TemplateTypeEnum::VERIFICATION);

        return $entity;
    }

    protected function getRepository(): TemplateRepository
    {
        return $this->repository;
    }
}
