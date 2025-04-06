# YunpianSmsBundle

云片短信服务集成包，用于集成[云片短信服务](https://www.yunpian.com/official/document/sms/zh_CN/domestic_list)的API。

## 功能特性

- 支持国内短信发送
- 支持短信模板管理
- 支持发送记录查询
- 支持余额查询
- 基于 Symfony 框架的完整集成

## 安装

1. 添加依赖：

```bash
composer require extra/yunpian-sms-bundle
```

2. 在 `config/bundles.php` 中注册 bundle：

```php
return [
    // ...
    YunpianSmsBundle\YunpianSmsBundle::class => ['all' => true],
];
```

## 配置

在 `.env` 文件中添加云片短信的配置：

```env
# 云片短信配置
YUNPIAN_API_KEY=your_api_key
YUNPIAN_API_VERSION=v2
```

## 使用方法

### 1. 发送短信

```php
use YunpianSmsBundle\Service\SmsService;

class YourService
{
    public function __construct(
        private readonly SmsService $smsService,
    ) {
    }

    public function sendMessage(): void
    {
        $this->smsService->send(
            mobile: '13800138000',
            content: '您的验证码是1234'
        );
    }
}
```

### 2. 使用模板发送短信

```php
$this->smsService->sendTemplate(
    mobile: '13800138000',
    templateId: 'your_template_id',
    templateData: ['code' => '1234']
);
```

### 3. 查询发送记录

```php
$records = $this->smsService->getRecords(
    startDate: new \DateTime('-7 days'),
    endDate: new \DateTime()
);
```

### 4. 查询余额

```php
$balance = $this->smsService->getBalance();
```

## API 文档

详细的 API 文档请参考：[云片短信官方文档](https://www.yunpian.com/official/document/sms/zh_CN/domestic_list)

## 依赖

- PHP >= 8.1
- Symfony Framework
- AmisBundle

## 许可证

This bundle is under the MIT license.
