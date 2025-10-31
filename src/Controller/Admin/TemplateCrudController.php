<?php

namespace YunpianSmsBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use YunpianSmsBundle\Entity\Template;
use YunpianSmsBundle\Enum\NotifyTypeEnum;
use YunpianSmsBundle\Enum\TemplateTypeEnum;

/**
 * @extends AbstractCrudController<Template>
 */
#[AdminCrud(routePath: '/yunpian/template', routeName: 'yunpian_template')]
final class TemplateCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Template::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('短信模板')
            ->setEntityLabelInPlural('短信模板')
            ->setPageTitle('index', '短信模板列表')
            ->setPageTitle('new', '创建短信模板')
            ->setPageTitle('edit', '编辑短信模板')
            ->setPageTitle('detail', '短信模板详情')
            ->setHelp('index', '管理云片短信模板配置')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['tplId', 'title', 'content'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->setMaxLength(9999)->hideOnForm();
        yield AssociationField::new('account', '账号')
            ->setHelp('选择对应的云片账号')
        ;
        yield TextField::new('tplId', '模板ID')
            ->setHelp('云片平台的模板ID')
            ->hideOnForm()
        ;
        yield TextField::new('title', '模板标题')
            ->setHelp('模板的显示标题')
        ;
        yield TextareaField::new('content', '模板内容')
            ->setNumOfRows(4)
            ->setHelp('短信模板内容，支持变量替换')
        ;
        yield TextField::new('checkStatus', '审核状态')
            ->hideOnForm()
            ->formatValue(function ($value) {
                return match ($value) {
                    'SUCCESS' => '✅ 审核通过',
                    'CHECKING' => '⏳ 审核中',
                    'FAIL' => '❌ 审核失败',
                    default => $value ?? '未知',
                };
            })
        ;
        yield TextareaField::new('checkReply', '审核说明')
            ->setNumOfRows(2)
            ->hideOnIndex()
            ->hideOnForm()
        ;
        yield ChoiceField::new('notifyType', '通知方式')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions(['class' => NotifyTypeEnum::class])
            ->formatValue(function ($value) {
                return $value instanceof NotifyTypeEnum ? $value->getLabel() : '';
            })
        ;
        yield ChoiceField::new('templateType', '模板类型')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions(['class' => TemplateTypeEnum::class])
            ->formatValue(function ($value) {
                return $value instanceof TemplateTypeEnum ? $value->getLabel() : '';
            })
        ;
        yield TextField::new('website', '官网地址')
            ->hideOnIndex()
            ->setHelp('验证码类模板需要填写对应的官网注册页面')
        ;
        yield TextareaField::new('applyDescription', '申请说明')
            ->setNumOfRows(3)
            ->hideOnIndex()
            ->setHelp('说明模板的发送场景和对象')
        ;
        yield TextField::new('callback', '回调地址')
            ->hideOnIndex()
            ->setHelp('审核结果回调通知地址')
        ;
        yield BooleanField::new('valid', '是否有效');
        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;
        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        $notifyTypeChoices = [];
        foreach (NotifyTypeEnum::cases() as $case) {
            $notifyTypeChoices[$case->getLabel()] = $case->value;
        }

        $templateTypeChoices = [];
        foreach (TemplateTypeEnum::cases() as $case) {
            $templateTypeChoices[$case->getLabel()] = $case->value;
        }

        return $filters
            ->add(EntityFilter::new('account', '账号'))
            ->add(TextFilter::new('tplId', '模板ID'))
            ->add(TextFilter::new('title', '模板标题'))
            ->add(TextFilter::new('checkStatus', '审核状态'))
            ->add(ChoiceFilter::new('notifyType', '通知方式')->setChoices($notifyTypeChoices))
            ->add(ChoiceFilter::new('templateType', '模板类型')->setChoices($templateTypeChoices))
            ->add(BooleanFilter::new('valid', '是否有效'))
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->setPermission(Action::DETAIL, 'ROLE_ADMIN')
            ->setPermission(Action::EDIT, 'ROLE_ADMIN')
            ->setPermission(Action::DELETE, 'ROLE_ADMIN')
        ;
    }
}
