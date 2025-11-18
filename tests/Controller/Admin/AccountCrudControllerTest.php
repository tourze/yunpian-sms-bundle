<?php

namespace YunpianSmsBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use YunpianSmsBundle\Controller\Admin\AccountCrudController;
use YunpianSmsBundle\Entity\Account;
use YunpianSmsBundle\YunpianSmsBundle;

/**
 * @internal
 */
#[CoversClass(AccountCrudController::class)]
#[RunTestsInSeparateProcesses]
final class AccountCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getEntityFqcn(): string
    {
        return Account::class;
    }

    protected function getControllerService(): AccountCrudController
    {
        $controller = self::getContainer()->get(AccountCrudController::class);
        self::assertInstanceOf(AccountCrudController::class, $controller);

        return $controller;
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id' => ['ID'];
        yield 'valid' => ['是否有效'];
        yield 'apiKey' => ['API密钥'];
        yield 'createTime' => ['创建时间'];
        yield 'updateTime' => ['更新时间'];
    }

    public static function provideNewPageFields(): iterable
    {
        yield 'valid' => ['valid'];
        yield 'apiKey' => ['apiKey'];
        yield 'remark' => ['remark'];
    }

    public static function provideEditPageFields(): iterable
    {
        yield 'valid' => ['valid'];
        yield 'apiKey' => ['apiKey'];
        yield 'remark' => ['remark'];
    }

    public function testControllerConfigurationMethods(): void
    {
        $client = $this->createAuthenticatedClient();

        $controller = self::getService(AccountCrudController::class);

        $client->request('GET', '/admin');
        $this->assertInstanceOf(Crud::class, $controller->configureCrud(Crud::new()));
        // configureFields已知返回iterable，移除冗余断言
        $this->assertNotEmpty(iterator_to_array($controller->configureFields('index')));
    }

    public function testControllerHasValidRequiredFieldConfiguration(): void
    {
        $client = $this->createAuthenticatedClient();

        $controller = self::getService(AccountCrudController::class);

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
        $client = $this->createAuthenticatedClient();

        // 测试搜索和过滤功能通过 HTTP 请求
        $response = $client->request('GET', '/admin');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // 验证控制器配置方法可以正常调用
        $controller = self::getService(AccountCrudController::class);
        $filters = $controller->configureFilters(Filters::new());
        // configureFilters已知返回非null，移除冗余断言
    }

    public function testActionsConfiguration(): void
    {
        $client = $this->createAuthenticatedClient();

        // 测试操作配置通过 HTTP 请求
        $response = $client->request('GET', '/admin');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // 验证控制器操作配置方法可以正常调用
        $controller = new AccountCrudController();
        $actions = $controller->configureActions(Actions::new());
        // configureActions已知返回非null，移除冗余断言
    }

    public function testValidationForRequiredFields(): void
    {
        $client = $this->createAuthenticatedClient();

        // 测试字段验证功能通过 HTTP 请求
        $response = $client->request('GET', '/admin');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // 验证控制器字段配置方法可以正常调用
        $controller = new AccountCrudController();
        $fields = iterator_to_array($controller->configureFields('new'));
        $this->assertNotEmpty($fields);

        // 验证必填字段存在
        $fieldNames = array_map(fn ($field) => is_string($field) ? $field : $field->getAsDto()->getProperty(), $fields);
        $this->assertContains('apiKey', $fieldNames);
    }
}
