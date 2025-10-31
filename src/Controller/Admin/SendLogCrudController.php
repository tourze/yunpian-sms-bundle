<?php

namespace YunpianSmsBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use YunpianSmsBundle\Entity\SendLog;
use YunpianSmsBundle\Enum\SendStatusEnum;

/**
 * @extends AbstractCrudController<SendLog>
 */
#[AdminCrud(routePath: '/yunpian/send-log', routeName: 'yunpian_send_log')]
final class SendLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SendLog::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('发送记录')
            ->setEntityLabelInPlural('发送记录')
            ->setPageTitle('index', '短信发送记录')
            ->setPageTitle('new', '创建发送记录')
            ->setPageTitle('edit', '编辑发送记录')
            ->setPageTitle('detail', '发送记录详情')
            ->setHelp('index', '查看和管理短信发送记录')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['mobile', 'content', 'sid', 'uid'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->setMaxLength(9999)->hideOnForm();
        yield AssociationField::new('account', '账号')
            ->setHelp('发送短信使用的云片账号')
        ;
        yield AssociationField::new('template', '模板')
            ->setHelp('使用的短信模板（如果有）')
        ;
        yield TextField::new('mobile', '手机号')
            ->setHelp('接收短信的手机号码')
        ;
        yield TextareaField::new('content', '短信内容')
            ->setNumOfRows(3)
            ->formatValue(function ($value) {
                return $this->truncateContent(is_string($value) ? $value : null);
            })
        ;
        yield TextField::new('uid', '业务ID')
            ->hideOnIndex()
            ->setHelp('业务系统的唯一标识')
        ;
        yield TextField::new('sid', '云片短信ID')
            ->hideOnIndex()
            ->setHelp('云片平台返回的短信ID')
        ;
        yield IntegerField::new('count', '计费条数')
            ->setHelp('短信计费条数')
        ;
        yield MoneyField::new('fee', '费用')
            ->setCurrency('CNY')
            ->setStoredAsCents(false)
            ->setHelp('短信发送费用（元）')
        ;
        yield ChoiceField::new('status', '发送状态')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions(['class' => SendStatusEnum::class])
            ->formatValue(function ($value) {
                if ($value instanceof SendStatusEnum) {
                    return $this->formatStatus($value);
                }

                return '';
            })
        ;
        yield TextField::new('statusMsg', '状态说明')
            ->hideOnIndex()
            ->hideOnForm()
        ;
        yield DateTimeField::new('receiveTime', '用户接收时间')
            ->hideOnIndex()
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;
        yield TextField::new('errorMsg', '错误信息')
            ->hideOnIndex()
            ->hideOnForm()
        ;
        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;
        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;
    }

    /**
     * 截断短信内容用于列表显示
     */
    private function truncateContent(?string $content): string
    {
        if (null === $content) {
            return '';
        }

        return mb_strlen($content) > 30
            ? mb_substr($content, 0, 30) . '...'
            : $content;
    }

    /**
     * 格式化发送状态显示
     */
    private function formatStatus(SendStatusEnum $status): string
    {
        return match ($status) {
            SendStatusEnum::PENDING => '⏳ ' . $status->getLabel(),
            SendStatusEnum::SENDING => '📤 ' . $status->getLabel(),
            SendStatusEnum::SUCCESS => '✅ ' . $status->getLabel(),
            SendStatusEnum::FAILED => '❌ ' . $status->getLabel(),
            SendStatusEnum::DELIVERED => '📱 ' . $status->getLabel(),
            SendStatusEnum::UNDELIVERED => '📵 ' . $status->getLabel(),
        };
    }

    public function configureFilters(Filters $filters): Filters
    {
        $statusChoices = [];
        foreach (SendStatusEnum::cases() as $case) {
            $statusChoices[$case->getLabel()] = $case->value;
        }

        return $filters
            ->add(EntityFilter::new('account', '账号'))
            ->add(EntityFilter::new('template', '模板'))
            ->add(TextFilter::new('mobile', '手机号'))
            ->add(ChoiceFilter::new('status', '发送状态')->setChoices($statusChoices))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(TextFilter::new('sid', '云片短信ID'))
            ->add(TextFilter::new('uid', '业务ID'))
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::NEW, Action::EDIT)
            ->setPermission(Action::DETAIL, 'ROLE_ADMIN')
            ->setPermission(Action::DELETE, 'ROLE_ADMIN')
        ;
    }
}
