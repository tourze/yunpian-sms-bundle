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
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use YunpianSmsBundle\Entity\Sign;

#[AdminCrud(routePath: '/yunpian/sign', routeName: 'yunpian_sign')]
class SignCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Sign::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('短信签名')
            ->setEntityLabelInPlural('短信签名')
            ->setPageTitle('index', '短信签名列表')
            ->setPageTitle('new', '创建短信签名')
            ->setPageTitle('edit', '编辑短信签名')
            ->setPageTitle('detail', '短信签名详情')
            ->setHelp('index', '管理云片短信签名配置')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['sign', 'applyState', 'remark']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->setMaxLength(9999)->hideOnForm();
        yield AssociationField::new('account', '账号')
            ->setHelp('选择对应的云片账号');
        yield IntegerField::new('signId', '签名ID')
            ->hideOnForm()
            ->setHelp('云片平台返回的签名ID');
        yield TextField::new('sign', '签名内容')
            ->setHelp('短信签名内容，需要审核通过后才能使用');
        yield TextField::new('applyState', '审核状态')
            ->hideOnForm()
            ->formatValue(function ($value) {
                return $this->formatApplyState($value);
            });
        yield UrlField::new('website', '业务网址')
            ->hideOnIndex()
            ->setHelp('与签名相关的业务网站地址');
        yield BooleanField::new('notify', '短信通知结果')
            ->setHelp('是否通过短信通知审核结果');
        yield BooleanField::new('applyVip', '申请专用通道')
            ->setHelp('是否申请专用短信通道');
        yield BooleanField::new('isOnlyGlobal', '仅发国际短信')
            ->setHelp('是否仅用于发送国际短信');
        yield TextField::new('industryType', '所属行业')
            ->setHelp('签名所属的行业类型');
        yield IntegerField::new('proveType', '证明文件类型')
            ->hideOnIndex()
            ->setHelp('上传的证明文件类型');
        yield TextareaField::new('licenseUrls', '证明文件URL')
            ->setNumOfRows(2)
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return is_array($value) ? implode("\n", $value) : '';
            })
            ->setHelp('证明文件的存储URL列表');
        yield TextField::new('idCardName', '企业责任人姓名')
            ->hideOnIndex();
        yield TextField::new('idCardNumber', '身份证号')
            ->hideOnIndex()
            ->formatValue(function ($value) {
                return $this->maskIdCard($value);
            });
        yield TextareaField::new('idCardFront', '身份证正面')
            ->setNumOfRows(2)
            ->hideOnIndex()
            ->setHelp('身份证正面照片URL');
        yield TextareaField::new('idCardBack', '身份证反面')
            ->setNumOfRows(2)
            ->hideOnIndex()
            ->setHelp('身份证反面照片URL');
        yield ChoiceField::new('signUse', '签名用途')
            ->setChoices([
                '自用' => 0,
                '他用' => 1,
            ])
            ->formatValue(function ($value) {
                return $value === 0 ? '自用' : '他用';
            });
        yield BooleanField::new('valid', '是否有效');
        yield TextareaField::new('remark', '备注')
            ->setNumOfRows(3)
            ->hideOnIndex();
        yield DateTimeField::new('createdAt', '创建时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss');
        yield DateTimeField::new('updatedAt', '更新时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss');
    }

    /**
     * 格式化审核状态显示
     */
    private function formatApplyState(?string $state): string
    {
        if ($state === null) {
            return '';
        }

        return match ($state) {
            'SUCCESS' => '✅ 审核通过',
            'CHECKING' => '⏳ 审核中',
            'FAIL' => '❌ 审核失败',
            'PENDING' => '📝 待审核',
            default => $state,
        };
    }

    /**
     * 身份证号码脱敏显示
     */
    private function maskIdCard(?string $idCard): string
    {
        if ($idCard === null || strlen($idCard) < 8) {
            return $idCard ?? '';
        }

        return substr($idCard, 0, 4) . '****' . substr($idCard, -4);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('account', '账号'))
            ->add(TextFilter::new('sign', '签名内容'))
            ->add(TextFilter::new('applyState', '审核状态'))
            ->add(BooleanFilter::new('valid', '是否有效'))
            ->add(BooleanFilter::new('notify', '短信通知结果'))
            ->add(BooleanFilter::new('applyVip', '申请专用通道'))
            ->add(BooleanFilter::new('isOnlyGlobal', '仅发国际短信'))
            ->add(ChoiceFilter::new('signUse', '签名用途')->setChoices([
                '自用' => 0,
                '他用' => 1,
            ]));
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT, Action::DELETE]);
    }
}
