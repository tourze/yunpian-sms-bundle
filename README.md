# YunpianSmsBundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/yunpian-sms-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/yunpian-sms-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/yunpian-sms-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/yunpian-sms-bundle)
[![License](https://img.shields.io/packagist/l/tourze/yunpian-sms-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/yunpian-sms-bundle)

A Symfony bundle that integrates with [Yunpian SMS Service](https://www.yunpian.com/official/document/sms/zh_CN/domestic_list) API for sending and managing SMS messages.

## Features

- Send domestic SMS messages
- Manage SMS templates 
- Query sending records
- Check account balance
- Sign management
- Daily consumption statistics
- Full integration with Symfony framework

## Requirements

- PHP >= 8.1
- Symfony Framework >= 6.4
- Doctrine ORM

## Installation

### Step 1: Add the dependency with Composer

```bash
composer require tourze/yunpian-sms-bundle
```

### Step 2: Register the bundle in `config/bundles.php`

```php
return [
    // ...
    YunpianSmsBundle\YunpianSmsBundle::class => ['all' => true],
];
```

## Configuration

This bundle requires you to create at least one account in the database. The `Account` entity stores the API key required for authentication with Yunpian's services.

You can create an account by inserting a record into the `ims_yunpian_account` table or use the admin interface if available:

```php
use YunpianSmsBundle\Entity\Account;

// Create a new account
$account = new Account();
$account->setApiKey('your_yunpian_api_key');
$account->setValid(true);
$account->setRemark('Main Account');

// Persist the account
$entityManager->persist($account);
$entityManager->flush();
```

## Quick Start

### 1. Send SMS

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
            content: 'Your verification code is 1234'
        );
    }
}
```

### 2. Send SMS using template

```php
use YunpianSmsBundle\Repository\TemplateRepository;

// In your service method
$account = $this->accountRepository->findOneBy(['valid' => true]);
$template = $this->templateRepository->findOneBy(['tplId' => 'your_template_id']);

$this->sendLogService->sendTpl(
    account: $account,
    template: $template,
    mobile: '13800138000',
    tplValue: ['code' => '1234']
);
```

### 3. Query send records

```php
// Set up request parameters
$request = new GetSendRecordRequest();
$request->setAccount($account);
$request->setStartTime(new \DateTime('-7 days'));
$request->setEndTime(new \DateTime());

// Get the records
$response = $this->apiClient->request($request);
```

### 4. Manage templates

```php
// Sync templates from Yunpian to local database
$this->templateService->syncTemplates($account);

// Get all templates
$templates = $this->templateRepository->findBy(['account' => $account]);
```

## API Documentation

For detailed API documentation, please refer to the [Yunpian SMS Official Documentation](https://www.yunpian.com/official/document/sms/zh_CN/domestic_list)

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## License

This bundle is released under the MIT License. See the [LICENSE](LICENSE) file for more details.
