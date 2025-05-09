<?php

namespace YunpianSmsBundle\Tests\Mock;

use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\Entity\Template;

class MockHelper
{
    /**
     * 创建测试用的Account实例
     */
    public static function createAccount(string $apiKey = 'test-api-key', bool $valid = true): Account
    {
        $account = new Account();
        $account->setApiKey($apiKey);
        $account->setValid($valid);
        $account->setRemark('测试账号');
        return $account;
    }
    
    /**
     * 创建测试用的Template实例
     */
    public static function createTemplate(Account $account, string $tplId = 'template-001'): Template
    {
        $template = new Template();
        $template->setAccount($account);
        $template->setTplId($tplId);
        $template->setTitle('测试模板');
        $template->setContent('您的验证码是#code#');
        $template->setCheckStatus('SUCCESS');
        return $template;
    }

    /**
     * 创建测试用的Sign实例
     */
    public static function createSign(Account $account, string $sign = '测试签名'): \YunpianSmsBundle\Entity\Sign
    {
        $signEntity = new \YunpianSmsBundle\Entity\Sign();
        $signEntity->setAccount($account);
        $signEntity->setSign($sign);
        $signEntity->setApplyState('SUCCESS');
        $signEntity->setValid(true);
        return $signEntity;
    }
} 