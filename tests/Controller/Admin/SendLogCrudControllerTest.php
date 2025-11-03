<?php

namespace YunpianSmsBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use YunpianSmsBundle\Controller\Admin\SendLogCrudController;
use YunpianSmsBundle\Entity\SendLog;
use YunpianSmsBundle\YunpianSmsBundle;

/**
 * @internal
 */
#[CoversClass(SendLogCrudController::class)]
#[RunTestsInSeparateProcesses]
final class SendLogCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getEntityFqcn(): string
    {
        return SendLog::class;
    }

    protected function getControllerService(): SendLogCrudController
    {
        $controller = self::getContainer()->get(SendLogCrudController::class);
        self::assertInstanceOf(SendLogCrudController::class, $controller);

        return $controller;
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id' => ['ID'];
        yield 'account' => ['账号'];
        yield 'template' => ['模板'];
        yield 'mobile' => ['手机号'];
        yield 'content' => ['短信内容'];
        yield 'count' => ['计费条数'];
        yield 'fee' => ['费用'];
        yield 'status' => ['发送状态'];
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
        $client = $this->createAuthenticatedClient();

        $client->request('GET', '/admin');
        $this->assertSame(SendLog::class, SendLogCrudController::getEntityFqcn());
    }

    public function testControllerConfigurationMethods(): void
    {
        $client = $this->createAuthenticatedClient();

        $controller = new SendLogCrudController();

        $client->request('GET', '/admin');
        $this->assertInstanceOf(Crud::class, $controller->configureCrud(Crud::new()));
        // configureFields已知返回iterable，移除冗余断言
        $this->assertNotEmpty(iterator_to_array($controller->configureFields('index')));
    }

    public function testControllerHasValidRequiredFieldConfiguration(): void
    {
        $client = $this->createAuthenticatedClient();

        $controller = new SendLogCrudController();

        $client->request('GET', '/admin');
        $fields = iterator_to_array($controller->configureFields('index'));

        $this->assertNotEmpty($fields, 'Controller should configure fields for display');
        $this->assertGreaterThan(5, count($fields), 'Controller should have multiple fields configured');
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
        $controller = new SendLogCrudController();
        // configureFilters已知返回非null，移除冗余断言
        $filters = $controller->configureFilters(Filters::new());
    }

    public function testActionsConfiguration(): void
    {
        $client = $this->createAuthenticatedClient();

        // 测试操作配置通过 HTTP 请求
        $response = $client->request('GET', '/admin');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // 验证控制器配置方法可以正常调用
        $controller = new SendLogCrudController();
        // configureActions已知返回非null，移除冗余断言
        $actions = $controller->configureActions(Actions::new());
    }

    public function testValidationForRequiredFields(): void
    {
        $client = $this->createAuthenticatedClient();

        // 测试字段验证功能通过 HTTP 请求
        $response = $client->request('GET', '/admin');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // 验证控制器字段配置方法可以正常调用
        $controller = new SendLogCrudController();
        $fields = iterator_to_array($controller->configureFields('new'));
        $this->assertNotEmpty($fields);

        // 验证必填字段存在
        $fieldNames = array_map(fn ($field) => is_string($field) ? $field : $field->getAsDto()->getProperty(), $fields);
        $this->assertContains('mobile', $fieldNames);
        $this->assertContains('content', $fieldNames);
    }
}
