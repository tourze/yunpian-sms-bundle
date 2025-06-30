<?php

namespace YunpianSmsBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use YunpianSmsBundle\Entity\DailyConsumption;

#[AdminCrud(routePath: '/yunpian/daily-consumption', routeName: 'yunpian_daily_consumption')]
class DailyConsumptionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DailyConsumption::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('日消费统计')
            ->setEntityLabelInPlural('日消费统计')
            ->setPageTitle('index', '日消费统计列表')
            ->setPageTitle('new', '创建日消费统计')
            ->setPageTitle('edit', '编辑日消费统计')
            ->setPageTitle('detail', '日消费统计详情')
            ->setHelp('index', '查看云片短信每日消费统计数据')
            ->setDefaultSort(['date' => 'DESC'])
            ->setSearchFields(['date']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->setMaxLength(9999)->hideOnForm();
        yield AssociationField::new('account', '账号')
            ->setHelp('统计数据对应的云片账号');
        yield DateField::new('date', '统计日期')
            ->setHelp('消费统计的日期');
        yield IntegerField::new('totalCount', '总短信条数')
            ->setHelp('当日发送的短信总条数')
            ->formatValue(function ($value) {
                return number_format($value);
            });
        yield MoneyField::new('totalFee', '总费用')
            ->setCurrency('CNY')
            ->setStoredAsCents(false)
            ->setHelp('当日短信发送总费用（元）');
        yield IntegerField::new('totalSuccessCount', '成功条数')
            ->setHelp('当日成功发送的短信条数')
            ->formatValue(function ($value) {
                return number_format($value);
            });
        yield IntegerField::new('totalFailedCount', '失败条数')
            ->setHelp('当日发送失败的短信条数')
            ->formatValue(function ($value) {
                return number_format($value);
            });
        yield IntegerField::new('totalUnknownCount', '未知状态条数')
            ->setHelp('当日状态未知的短信条数')
            ->formatValue(function ($value) {
                return number_format($value);
            });
        yield TextareaField::new('items', '消费明细')
            ->setNumOfRows(5)
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return $this->formatItems($value);
            })
            ->setHelp('详细的消费统计数据（JSON格式）');
        yield DateTimeField::new('createdAt', '创建时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss');
        yield DateTimeField::new('updatedAt', '更新时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss');
    }

    /**
     * 格式化消费明细显示
     */
    private function formatItems(?array $items): string
    {
        if ($items === null || empty($items)) {
            return '暂无明细数据';
        }

        return json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('account', '账号'))
            ->add(DateTimeFilter::new('date', '统计日期'))
            ->add(NumericFilter::new('totalCount', '总短信条数'))
            ->add(NumericFilter::new('totalFee', '总费用'))
            ->add(NumericFilter::new('totalSuccessCount', '成功条数'))
            ->add(NumericFilter::new('totalFailedCount', '失败条数'));
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT, Action::DELETE])
            ->remove(Crud::PAGE_INDEX, Action::NEW); // 通常不手动创建统计数据
    }
}
