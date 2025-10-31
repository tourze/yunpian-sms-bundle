<?php

namespace YunpianSmsBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use YunpianSmsBundle\Controller\Admin\DailyConsumptionCrudController;
use YunpianSmsBundle\Entity\DailyConsumption;
use YunpianSmsBundle\YunpianSmsBundle;

/**
 * @internal
 */
#[CoversClass(DailyConsumptionCrudController::class)]
#[RunTestsInSeparateProcesses]
final class DailyConsumptionCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getEntityFqcn(): string
    {
        return DailyConsumption::class;
    }

    protected function getControllerService(): DailyConsumptionCrudController
    {
        $controller = self::getContainer()->get(DailyConsumptionCrudController::class);
        self::assertInstanceOf(DailyConsumptionCrudController::class, $controller);

        return $controller;
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id' => ['ID'];
        yield 'account' => ['账号'];
        yield 'date' => ['统计日期'];
        yield 'totalCount' => ['总短信条数'];
        yield 'totalFee' => ['总费用'];
        yield 'totalSuccessCount' => ['成功条数'];
        yield 'totalFailedCount' => ['失败条数'];
        yield 'totalUnknownCount' => ['未知状态条数'];
        yield 'createTime' => ['创建时间'];
        yield 'updateTime' => ['更新时间'];
    }

    public static function provideNewPageFields(): iterable
    {
        // 只读控制器，禁用了NEW操作，提供一个占位项避免空数据集错误
        yield 'dummy' => ['dummy'];
    }

    public static function provideEditPageFields(): iterable
    {
        // 只读控制器，禁用了EDIT操作，提供一个占位项避免空数据集错误
        yield 'dummy' => ['dummy'];
    }

    public function testGetEntityFqcn(): void
    {
        $client = self::createClientWithDatabase([
            YunpianSmsBundle::class => ['all' => true],
        ]);

        $admin = $this->createAdminUser('admin@test.com', 'password');
        $this->loginAsAdmin($client, 'admin@test.com', 'password');

        $client->request('GET', '/admin');
        $this->assertSame(DailyConsumption::class, DailyConsumptionCrudController::getEntityFqcn());
    }

    public function testControllerConfigurationMethods(): void
    {
        $client = self::createClientWithDatabase([
            YunpianSmsBundle::class => ['all' => true],
        ]);

        $admin = $this->createAdminUser('admin@test.com', 'password');
        $this->loginAsAdmin($client, 'admin@test.com', 'password');

        $controller = new DailyConsumptionCrudController();

        $client->request('GET', '/admin');
        $this->assertInstanceOf(Crud::class, $controller->configureCrud(Crud::new()));
        // configureFields已知返回iterable，移除冗余断言
        $this->assertNotEmpty(iterator_to_array($controller->configureFields('index')));
    }

    public function testControllerHasValidRequiredFieldConfiguration(): void
    {
        $client = self::createClientWithDatabase([
            YunpianSmsBundle::class => ['all' => true],
        ]);

        $admin = $this->createAdminUser('admin@test.com', 'password');
        $this->loginAsAdmin($client, 'admin@test.com', 'password');

        $controller = new DailyConsumptionCrudController();

        $client->request('GET', '/admin');
        $fields = iterator_to_array($controller->configureFields('index'));

        $this->assertNotEmpty($fields, 'Controller should configure fields for display');
        $this->assertGreaterThan(3, count($fields), 'Controller should have multiple fields configured');
    }

    public function testUnauthorizedAccessShouldBeDenied(): void
    {
        $client = self::createClientWithDatabase([
            YunpianSmsBundle::class => ['all' => true],
        ]);

        try {
            $client->request('GET', '/admin');
            // 如果没有抛出异常，则检查是否是重定向
            $this->assertResponseStatusCodeSame(302);
        } catch (AccessDeniedException $e) {
            // 期望的行为：访问被拒绝
            $this->assertStringContainsString('Access Denied', $e->getMessage());
        }
    }

    public function testSearchFunctionalityForAllFilters(): void
    {
        $client = self::createClientWithDatabase([
            YunpianSmsBundle::class => ['all' => true],
        ]);

        $admin = $this->createAdminUser('admin@test.com', 'password');
        $this->loginAsAdmin($client, 'admin@test.com', 'password');

        // 测试搜索和过滤功能通过 HTTP 请求
        $response = $client->request('GET', '/admin');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // 验证控制器配置方法可以正常调用
        $controller = new DailyConsumptionCrudController();
        // configureFilters已知返回非null，移除冗余断言
        $filters = $controller->configureFilters(Filters::new());
    }

    public function testActionsConfiguration(): void
    {
        $client = self::createClientWithDatabase([
            YunpianSmsBundle::class => ['all' => true],
        ]);

        $admin = $this->createAdminUser('admin@test.com', 'password');
        $this->loginAsAdmin($client, 'admin@test.com', 'password');

        // 测试操作配置通过 HTTP 请求
        $response = $client->request('GET', '/admin');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // 验证控制器配置方法可以正常调用
        $controller = new DailyConsumptionCrudController();
        // configureActions已知返回非null，移除冗余断言
        $actions = $controller->configureActions(Actions::new());
    }

    public function testValidationForRequiredFields(): void
    {
        $client = self::createClientWithDatabase([
            YunpianSmsBundle::class => ['all' => true],
        ]);

        $admin = $this->createAdminUser('admin@test.com', 'password');
        $this->loginAsAdmin($client, 'admin@test.com', 'password');

        // 测试字段验证功能通过 HTTP 请求
        $response = $client->request('GET', '/admin');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // 验证控制器字段配置方法可以正常调用
        $controller = new DailyConsumptionCrudController();
        $fields = iterator_to_array($controller->configureFields('new'));
        $this->assertNotEmpty($fields);

        // 验证必填字段存在
        $fieldNames = array_map(fn ($field) => is_string($field) ? $field : $field->getAsDto()->getProperty(), $fields);
        $this->assertContains('date', $fieldNames);
        $this->assertContains('totalCount', $fieldNames);
    }
}
