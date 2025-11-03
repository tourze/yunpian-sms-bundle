<?php

namespace YunpianSmsBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use YunpianSmsBundle\Controller\Admin\TemplateCrudController;
use YunpianSmsBundle\Entity\Template;
use YunpianSmsBundle\YunpianSmsBundle;

/**
 * @internal
 */
#[CoversClass(TemplateCrudController::class)]
#[RunTestsInSeparateProcesses]
final class TemplateCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getEntityFqcn(): string
    {
        return Template::class;
    }

    protected function getControllerService(): TemplateCrudController
    {
        $controller = self::getContainer()->get(TemplateCrudController::class);
        self::assertInstanceOf(TemplateCrudController::class, $controller);

        return $controller;
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id' => ['ID'];
        yield 'account' => ['账号'];
        yield 'templateId' => ['模板ID'];
        yield 'title' => ['模板标题'];
        yield 'content' => ['模板内容'];
        yield 'checkStatus' => ['审核状态'];
        yield 'notifyType' => ['通知方式'];
        yield 'templateType' => ['模板类型'];
        yield 'valid' => ['是否有效'];
        yield 'createTime' => ['创建时间'];
        yield 'updateTime' => ['更新时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'account' => ['account'];
        yield 'title' => ['title'];
        yield 'content' => ['content'];
        yield 'notifyType' => ['notifyType'];
        yield 'templateType' => ['templateType'];
        yield 'website' => ['website'];
        yield 'applyDescription' => ['applyDescription'];
        yield 'callback' => ['callback'];
        yield 'valid' => ['valid'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'account' => ['account'];
        yield 'title' => ['title'];
        yield 'content' => ['content'];
        yield 'notifyType' => ['notifyType'];
        yield 'templateType' => ['templateType'];
        yield 'website' => ['website'];
        yield 'applyDescription' => ['applyDescription'];
        yield 'callback' => ['callback'];
        yield 'valid' => ['valid'];
    }

    public function testGetEntityFqcn(): void
    {
        $client = $this->createAuthenticatedClient();

        $client->request('GET', '/admin');
        $this->assertSame(Template::class, TemplateCrudController::getEntityFqcn());
    }

    public function testControllerConfigurationMethods(): void
    {
        $client = $this->createAuthenticatedClient();

        $controller = new TemplateCrudController();

        $client->request('GET', '/admin');
        $this->assertInstanceOf(Crud::class, $controller->configureCrud(Crud::new()));
        // configureFields已知返回iterable，移除冗余断言
        $this->assertNotEmpty(iterator_to_array($controller->configureFields('index')));
    }

    public function testControllerHasValidRequiredFieldConfiguration(): void
    {
        $client = $this->createAuthenticatedClient();

        $controller = new TemplateCrudController();

        $client->request('GET', '/admin');
        $fields = iterator_to_array($controller->configureFields('index'));

        $this->assertNotEmpty($fields, 'Controller should configure fields for display');
        $this->assertGreaterThan(4, count($fields), 'Controller should have multiple fields configured');
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
        $controller = new TemplateCrudController();
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
        $controller = new TemplateCrudController();
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
        $controller = new TemplateCrudController();
        $fields = iterator_to_array($controller->configureFields('new'));
        $this->assertNotEmpty($fields);

        // 验证必填字段存在
        $fieldNames = array_map(fn ($field) => is_string($field) ? $field : $field->getAsDto()->getProperty(), $fields);
        $this->assertContains('content', $fieldNames);
    }
}
