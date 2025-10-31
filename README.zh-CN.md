# YunpianSmsBundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/yunpian-sms-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/yunpian-sms-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/yunpian-sms-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/yunpian-sms-bundle)
[![License](https://img.shields.io/packagist/l/tourze/yunpian-sms-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/yunpian-sms-bundle)
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/yunpian-sms-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/yunpian-sms-bundle)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/php-monorepo/main.svg?style=flat-square)](https://codecov.io/gh/tourze/php-monorepo)

一个集成[云片短信服务][yunpian-docs] API 的 Symfony 包，用于发送和管理短信消息。

## 目录

- [功能特性](#功能特性)
- [系统要求](#系统要求)  
- [安装](#安装)
- [配置](#配置)
- [快速开始](#快速开始)
- [控制台命令](#控制台命令)
- [高级用法](#高级用法)
- [API文档](#api文档)
- [测试](#测试)
- [贡献指南](#贡献指南)
- [安全问题](#安全问题)
- [致谢](#致谢)
- [许可证](#许可证)

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

## 控制台命令

本包提供了多个控制台命令，用于与云片短信平台进行数据同步：

### `yunpian:sync-daily-consumption`

同步所有有效账户的短信日消耗数据。

```bash
# 同步昨天的消耗数据（默认）
php bin/console yunpian:sync-daily-consumption

# 同步指定日期的消耗数据
php bin/console yunpian:sync-daily-consumption --date=2024-01-15
```

**选项：**
- `--date` / `-d`：同步指定日期的数据（格式：Y-m-d）

**定时任务：** 此命令每4小时的第15分钟自动执行（cron：`15 */4 * * *`）

### `yunpian:sync-send-record`

同步所有有效账户的短信发送记录。

```bash
# 同步最近的发送记录
php bin/console yunpian:sync-send-record

# 同步指定时间范围内的记录
php bin/console yunpian:sync-send-record --start-time="2024-01-01 00:00:00" --end-time="2024-01-31 23:59:59"

# 同步指定手机号的记录
php bin/console yunpian:sync-send-record --mobile=13800138000
```

**选项：**
- `--start-time` / `-s`：同步的开始时间（格式：Y-m-d H:i:s）
- `--end-time` / `-e`：同步的结束时间（格式：Y-m-d H:i:s）
- `--mobile` / `-m`：按手机号筛选

**定时任务：** 此命令每4小时自动执行（cron：`0 */4 * * *`）

### `yunpian:sync-send-status`

同步待处理短信的发送状态更新。

```bash
php bin/console yunpian:sync-send-status
```

**定时任务：** 此命令每5分钟自动执行（cron：`*/5 * * * *`）

### `yunpian:sync-sign`

从云片平台同步短信签名。

```bash
php bin/console yunpian:sync-sign
```

**注意：** 此命令需要在签名更新时手动执行。

### `yunpian:sync-template`

从云片平台同步短信模板。

```bash
php bin/console yunpian:sync-template
```

**注意：** 此命令需要在模板更新时手动执行。

## 高级用法

### 自定义短信传输

您可以将云片短信传输与 Symfony Notifier 一起使用：

```php
use YunpianSmsBundle\Service\NotifierSmsTransport;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\SmsRecipientInterface;

// 在您的服务中
$transport = $this->container->get(NotifierSmsTransport::class);
$notification = new Notification('您的验证码是 1234');
$recipient = new SmsRecipient('13800138000');

$transport->send($notification, $recipient);
```

### 事件订阅者

本包提供事件订阅者用于自动模板和签名同步：

- `TemplateSubscriber`：模板实体修改时自动同步模板
- `SignSubscriber`：签名实体修改时自动同步签名

### 管理界面

如果您在使用 EasyAdmin，本包提供了以下 CRUD 控制器：

- 账号管理（`AccountCrudController`）
- 模板管理（`TemplateCrudController`） 
- 发送日志（`SendLogCrudController`）
- 签名管理（`SignCrudController`）
- 每日消费（`DailyConsumptionCrudController`）

## API文档

详细的API文档请参考：[云片短信官方文档][yunpian-docs]

## 测试

使用 PHPUnit 运行测试套件：

```bash
./vendor/bin/phpunit packages/yunpian-sms-bundle/tests
```

## 贡献指南

请查看[CONTRIBUTING.md](CONTRIBUTING.md)了解详情。

## 安全问题

如果您发现任何安全相关问题，请通过邮件 security@tourze.com 联系我们，而不是使用问题追踪器。

## 致谢

- [Tourze](https://github.com/tourze)
- [所有贡献者](../../contributors)

## 许可证

本软件包基于MIT许可证发布。详情请查看[LICENSE](LICENSE)文件。

[yunpian-docs]: https://www.yunpian.com/official/document/sms/zh_CN/domestic_list
