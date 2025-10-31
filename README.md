# YunpianSmsBundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/yunpian-sms-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/yunpian-sms-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/yunpian-sms-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/yunpian-sms-bundle)
[![License](https://img.shields.io/packagist/l/tourze/yunpian-sms-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/yunpian-sms-bundle)
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/yunpian-sms-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/yunpian-sms-bundle)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/php-monorepo/main.svg?style=flat-square)](https://codecov.io/gh/tourze/php-monorepo)

A Symfony bundle that integrates with [Yunpian SMS Service][yunpian-docs] API for sending and managing SMS messages.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Quick Start](#quick-start)
- [Console Commands](#console-commands)
- [Advanced Usage](#advanced-usage)
- [API Documentation](#api-documentation)
- [Testing](#testing)
- [Contributing](#contributing)
- [Security](#security)
- [Credits](#credits)
- [License](#license)

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

This bundle requires you to create at least one account in the database. 
The `Account` entity stores the API key required for authentication with Yunpian's services.

You can create an account by inserting a record into the `ims_yunpian_account` table 
or use the admin interface if available:

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

## Console Commands

This bundle provides several console commands for synchronizing data with Yunpian SMS platform:

### `yunpian:sync-daily-consumption`

Synchronize daily SMS consumption data for all valid accounts.

```bash
# Sync yesterday's consumption data (default)
php bin/console yunpian:sync-daily-consumption

# Sync consumption data for a specific date
php bin/console yunpian:sync-daily-consumption --date=2024-01-15
```

**Options:**
- `--date` / `-d`: Sync data for a specific date (format: Y-m-d)

**Scheduled Task:** This command runs automatically every 4 hours at the 15th minute (cron: `15 */4 * * *`)

### `yunpian:sync-send-record`

Synchronize SMS sending records for all valid accounts.

```bash
# Sync recent send records
php bin/console yunpian:sync-send-record

# Sync records within a specific time range
php bin/console yunpian:sync-send-record \
  --start-time="2024-01-01 00:00:00" \
  --end-time="2024-01-31 23:59:59"

# Sync records for a specific mobile number
php bin/console yunpian:sync-send-record --mobile=13800138000
```

**Options:**
- `--start-time` / `-s`: Start time for synchronization (format: Y-m-d H:i:s)
- `--end-time` / `-e`: End time for synchronization (format: Y-m-d H:i:s)
- `--mobile` / `-m`: Filter by mobile number

**Scheduled Task:** This command runs automatically every 4 hours (cron: `0 */4 * * *`)

### `yunpian:sync-send-status`

Synchronize SMS sending status updates for pending messages.

```bash
php bin/console yunpian:sync-send-status
```

**Scheduled Task:** This command runs automatically every 5 minutes (cron: `*/5 * * * *`)

### `yunpian:sync-sign`

Synchronize SMS signatures from Yunpian platform.

```bash
php bin/console yunpian:sync-sign
```

**Note:** This command needs to be run manually when signature updates are required.

### `yunpian:sync-template`

Synchronize SMS templates from Yunpian platform.

```bash
php bin/console yunpian:sync-template
```

**Note:** This command needs to be run manually when template updates are required.

## Advanced Usage

### Custom SMS Transport

You can use the Yunpian SMS transport with Symfony Notifier:

```php
use YunpianSmsBundle\Service\NotifierSmsTransport;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\SmsRecipientInterface;

// In your service
$transport = $this->container->get(NotifierSmsTransport::class);
$notification = new Notification('Your verification code is 1234');
$recipient = new SmsRecipient('13800138000');

$transport->send($notification, $recipient);
```

### Event Subscribers

The bundle provides event subscribers for automatic template and signature synchronization:

- `TemplateSubscriber`: Automatically syncs templates when template entities are modified
- `SignSubscriber`: Automatically syncs signatures when sign entities are modified

### Admin Interface

If you're using EasyAdmin, this bundle provides CRUD controllers for managing:

- Accounts (`AccountCrudController`)
- Templates (`TemplateCrudController`) 
- Send Logs (`SendLogCrudController`)
- Signs (`SignCrudController`)
- Daily Consumption (`DailyConsumptionCrudController`)

## API Documentation

For detailed API documentation, please refer to the [Yunpian SMS Official Documentation][yunpian-docs]

## Testing

Run the test suite with PHPUnit:

```bash
./vendor/bin/phpunit packages/yunpian-sms-bundle/tests
```

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email security@tourze.com instead of using the issue tracker.

## Credits

- [Tourze](https://github.com/tourze)
- [All Contributors](../../contributors)

## License

This bundle is released under the MIT License. See the [LICENSE](LICENSE) file for more details.

[yunpian-docs]: https://www.yunpian.com/official/document/sms/zh_CN/domestic_list
