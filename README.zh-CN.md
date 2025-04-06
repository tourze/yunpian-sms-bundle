# YunpianSmsBundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/yunpian-sms-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/yunpian-sms-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/yunpian-sms-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/yunpian-sms-bundle)
[![License](https://img.shields.io/packagist/l/tourze/yunpian-sms-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/yunpian-sms-bundle)

云片短信服务集成包，用于集成[云片短信服务](https://www.yunpian.com/official/document/sms/zh_CN/domestic_list)的API。

## 功能特性

- 支持国内短信发送
- 支持短信模板管理
- 支持发送记录查询
- 支持余额查询
- 签名管理
- 每日消费统计
- 基于 Symfony 框架的完整集成

## 系统要求

- PHP >= 8.1
- Symfony Framework >= 6.4
- Doctrine ORM

## 安装

### 步骤1: 使用Composer添加依赖

```bash
composer require tourze/yunpian-sms-bundle
```

### 步骤2: 在`config/bundles.php`中注册bundle

```php
return [
    // ...
    YunpianSmsBundle\YunpianSmsBundle::class => ['all' => true],
];
```

## 配置

本bundle需要您在数据库中创建至少一个账号。`Account`实体用于存储与云片服务进行认证所需的API密钥。

您可以通过向`ims_yunpian_account`表中插入记录或使用管理界面（如果可用）来创建账号：

```php
use YunpianSmsBundle\Entity\Account;

// 创建新账号
$account = new Account();
$account->setApiKey('您的云片API密钥');
$account->setValid(true);
$account->setRemark('主账号');

// 持久化账号
$entityManager->persist($account);
$entityManager->flush();
```

## 快速开始

### 1. 发送短信

```php
use YunpianSmsBundle\Service\SendLogService;
use YunpianSmsBundle\Repository\AccountRepository;

class YourService
{
    public function __construct(
        private readonly SendLogService $sendLogService,
        private readonly AccountRepository $accountRepository,
    ) {
    }

    public function sendMessage(): void
    {
        $account = $this->accountRepository->findOneBy(['valid' => true]);

        $this->sendLogService->send(
            account: $account,
            mobile: '13800138000',
            content: '您的验证码是1234'
        );
    }
}
```

### 2. 使用模板发送短信

```php
use YunpianSmsBundle\Repository\TemplateRepository;

// 在你的服务方法中
$account = $this->accountRepository->findOneBy(['valid' => true]);
$template = $this->templateRepository->findOneBy(['tplId' => 'your_template_id']);

$this->sendLogService->sendTpl(
    account: $account,
    template: $template,
    mobile: '13800138000',
    tplValue: ['code' => '1234']
);
```

### 3. 查询发送记录

```php
// 设置请求参数
$request = new GetSendRecordRequest();
$request->setAccount($account);
$request->setStartTime(new \DateTime('-7 days'));
$request->setEndTime(new \DateTime());

// 获取记录
$response = $this->apiClient->request($request);
```

### 4. 管理模板

```php
// 从云片同步模板到本地数据库
$this->templateService->syncTemplates($account);

// 获取所有模板
$templates = $this->templateRepository->findBy(['account' => $account]);
```

## API文档

详细的API文档请参考：[云片短信官方文档](https://www.yunpian.com/official/document/sms/zh_CN/domestic_list)

## 贡献指南

请查看[CONTRIBUTING.md](CONTRIBUTING.md)了解详情。

## 许可证

本软件包基于MIT许可证发布。详情请查看[LICENSE](LICENSE)文件。
