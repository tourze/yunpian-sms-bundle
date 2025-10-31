<?php

namespace YunpianSmsBundle\Tests\Mock;

use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\Template;
use YunpianSmsBundle\Enum\NotifyTypeEnum;
use YunpianSmsBundle\Enum\TemplateTypeEnum;

class MockHelper
{
    public static function createAccount(): Account
    {
        $account = new Account();
        $account->setApiKey('test-api-key-' . uniqid());
        $account->setValid(true);
        $account->setRemark('Test Account');

        return $account;
    }

    public static function createTemplate(Account $account): Template
    {
        $template = new Template();
        $template->setAccount($account);
        $template->setTplId('123456789');
        $template->setTitle('Test Template');
        $template->setContent('Test content with #var# placeholder');
        $template->setCheckStatus('approved');
        $template->setCheckReply('Template approved');
        $template->setNotifyType(NotifyTypeEnum::ALWAYS);
        $template->setTemplateType(TemplateTypeEnum::NOTIFICATION);
        $template->setWebsite('https://example.com');
        $template->setApplyDescription('Test template for unit testing');
        $template->setCallback('https://example.com/callback');
        $template->setValid(true);

        return $template;
    }
}
