<?php

namespace YunpianSmsBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\Template;
use YunpianSmsBundle\Enum\NotifyTypeEnum;
use YunpianSmsBundle\Enum\TemplateTypeEnum;

/**
 * @internal
 */
#[CoversClass(Template::class)]
final class TemplateTest extends AbstractEntityTestCase
{
    private Account $account;

    protected function createEntity(): Template
    {
        return new Template();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->account = new Account();
        $this->account->setApiKey('test-api-key');
        $this->account->setValid(true);
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        $account = new Account();
        $account->setApiKey('test-api-key');
        $account->setValid(true);

        return [
            'account' => ['account', $account],
            'tplId' => ['tplId', 'test-template-id'],
            'title' => ['title', 'Test Template'],
            'content' => ['content', 'Test content'],
            'checkStatus' => ['checkStatus', 'SUCCESS'],
            'checkReply' => ['checkReply', 'Test reply'],
            'notifyType' => ['notifyType', NotifyTypeEnum::ALWAYS],
            'templateType' => ['templateType', TemplateTypeEnum::NOTIFICATION],
            'valid' => ['valid', true],
            'createTime' => ['createTime', new \DateTimeImmutable()],
            'updateTime' => ['updateTime', new \DateTimeImmutable()],
        ];
    }

    public function testIdGetter(): void
    {
        $template = new Template();
        $this->assertEquals(0, $template->getId());
    }

    public function testDefaultValuesAreSetCorrectly(): void
    {
        $template = new Template();
        $this->assertNull($template->getCreateTime());
        $this->assertNull($template->getUpdateTime());
        $this->assertEquals(0, $template->getId());
    }

    public function testConstructor(): void
    {
        $template = new Template();

        // 验证默认值
        $this->assertEquals(0, $template->getId());
        $this->assertSame(NotifyTypeEnum::ALWAYS, $template->getNotifyType());
        $this->assertSame(TemplateTypeEnum::NOTIFICATION, $template->getTemplateType());
        $this->assertFalse($template->isValid());
    }

    public function testSetAndGetAccount(): void
    {
        $template = new Template();

        $template->setAccount($this->account);
        $this->assertSame($this->account, $template->getAccount());
    }

    public function testSetAndGetTplId(): void
    {
        $template = new Template();

        $tplId = 'template-001';
        $template->setTplId($tplId);

        $this->assertSame($tplId, $template->getTplId());
    }

    public function testSetAndGetContent(): void
    {
        $template = new Template();

        $content = '您的验证码是#code#，有效期#time#分钟';
        $template->setContent($content);

        $this->assertSame($content, $template->getContent());
    }

    public function testSetAndGetCheckStatus(): void
    {
        $template = new Template();

        $checkStatus = 'SUCCESS';
        $template->setCheckStatus($checkStatus);

        $this->assertSame($checkStatus, $template->getCheckStatus());
    }

    public function testSetAndGetCheckReply(): void
    {
        $template = new Template();

        $checkReply = '内容不合规';
        $template->setCheckReply($checkReply);

        $this->assertSame($checkReply, $template->getCheckReply());
    }

    public function testSetAndGetCreateTime(): void
    {
        $template = new Template();

        $createTime = new \DateTimeImmutable('2023-05-01 12:00:00');
        $template->setCreateTime($createTime);

        $this->assertSame($createTime, $template->getCreateTime());
    }

    public function testSetAndGetUpdateTime(): void
    {
        $template = new Template();

        $updateTime = new \DateTimeImmutable('2023-05-01 12:00:00');
        $template->setUpdateTime($updateTime);

        $this->assertSame($updateTime, $template->getUpdateTime());
    }

    public function testCompleteSetup(): void
    {
        $template = new Template();

        $template->setAccount($this->account);
        $template->setTplId('template-001');
        $template->setTitle('验证码模板');
        $template->setContent('您的验证码是#code#，有效期#time#分钟');
        $template->setCheckStatus('SUCCESS');
        $template->setCheckReply(null);
        $template->setCreateTime(new \DateTimeImmutable('2023-05-01 12:00:00'));

        $this->assertSame($this->account, $template->getAccount());
        $this->assertSame('template-001', $template->getTplId());
        $this->assertSame('验证码模板', $template->getTitle());
        $this->assertSame('您的验证码是#code#，有效期#time#分钟', $template->getContent());
        $this->assertSame('SUCCESS', $template->getCheckStatus());
        $this->assertNull($template->getCheckReply());
        $this->assertNotNull($template->getCreateTime());
        $this->assertEquals('2023-05-01 12:00:00', $template->getCreateTime()->format('Y-m-d H:i:s'));
    }
}
