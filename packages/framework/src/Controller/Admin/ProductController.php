<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Doctrine\ORM\QueryBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\FrameworkBundle\Component\Controller\AdminBaseController;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Grid\GridFactory;
use Shopsys\FrameworkBundle\Component\Grid\QueryBuilderWithRowManipulatorDataSource;
use Shopsys\FrameworkBundle\Component\Router\Security\Annotation\CsrfProtection;
use Shopsys\FrameworkBundle\Form\Admin\Product\ProductEditFormType;
use Shopsys\FrameworkBundle\Form\Admin\Product\ProductMassActionFormType;
use Shopsys\FrameworkBundle\Form\Admin\Product\VariantFormType;
use Shopsys\FrameworkBundle\Form\Admin\QuickSearch\QuickSearchFormData;
use Shopsys\FrameworkBundle\Form\Admin\QuickSearch\QuickSearchFormType;
use Shopsys\FrameworkBundle\Model\Administrator\AdministratorGridFacade;
use Shopsys\FrameworkBundle\Model\AdminNavigation\Breadcrumb;
use Shopsys\FrameworkBundle\Model\AdminNavigation\MenuItem;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\AdvancedSearchFacade;
use Shopsys\FrameworkBundle\Model\Category\CategoryFacade;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade;
use Shopsys\FrameworkBundle\Model\Product\Listing\ProductListAdminFacade;
use Shopsys\FrameworkBundle\Model\Product\MassAction\ProductMassActionFacade;
use Shopsys\FrameworkBundle\Model\Product\Pricing\AdminProductPriceCalculationFacade;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductDataFactory;
use Shopsys\FrameworkBundle\Model\Product\ProductFacade;
use Shopsys\FrameworkBundle\Model\Product\ProductVariantFacade;
use Shopsys\FrameworkBundle\Twig\ProductExtension;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AdminBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\CategoryFacade
     */
    private $categoryFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\MassAction\ProductMassActionFacade
     */
    private $productMassActionFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Grid\GridFactory
     */
    private $gridFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductFacade
     */
    private $productFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductDataFactory
     */
    private $productDataFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\AdminProductPriceCalculationFacade
     */
    private $adminProductPriceCalculationFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\AdminNavigation\Breadcrumb
     */
    private $breadcrumb;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade
     */
    private $pricingGroupFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Administrator\AdministratorGridFacade
     */
    private $administratorGridFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Listing\ProductListAdminFacade
     */
    private $productListAdminFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\AdvancedSearch\AdvancedSearchFacade
     */
    private $advancedSearchFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductVariantFacade
     */
    private $productVariantFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Twig\ProductExtension
     */
    private $productExtension;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    public function __construct(
        CategoryFacade $categoryFacade,
        ProductMassActionFacade $productMassActionFacade,
        GridFactory $gridFactory,
        ProductFacade $productFacade,
        ProductDataFactory $productDataFactory,
        AdminProductPriceCalculationFacade $adminProductPriceCalculationFacade,
        Breadcrumb $breadcrumb,
        PricingGroupFacade $pricingGroupFacade,
        AdministratorGridFacade $administratorGridFacade,
        ProductListAdminFacade $productListAdminFacade,
        AdvancedSearchFacade $advancedSearchFacade,
        ProductVariantFacade $productVariantFacade,
        ProductExtension $productExtension,
        Domain $domain
    ) {
        $this->categoryFacade = $categoryFacade;
        $this->productMassActionFacade = $productMassActionFacade;
        $this->gridFactory = $gridFactory;
        $this->productFacade = $productFacade;
        $this->productDataFactory = $productDataFactory;
        $this->adminProductPriceCalculationFacade = $adminProductPriceCalculationFacade;
        $this->breadcrumb = $breadcrumb;
        $this->pricingGroupFacade = $pricingGroupFacade;
        $this->administratorGridFacade = $administratorGridFacade;
        $this->productListAdminFacade = $productListAdminFacade;
        $this->advancedSearchFacade = $advancedSearchFacade;
        $this->productVariantFacade = $productVariantFacade;
        $this->productExtension = $productExtension;
        $this->domain = $domain;
    }

    /**
     * @Route("/product/edit/{id}", requirements={"id" = "\d+"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function editAction(Request $request, $id)
    {
        $product = $this->productFacade->getById($id);
        $productData = $this->productDataFactory->createFromProduct($product);

        $form = $this->createForm(ProductEditFormType::class, $productData, ['product' => $product]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->productFacade->edit($id, $form->getData());

            $this->getFlashMessageSender()->addSuccessFlashTwig(
                t('Product <strong>{{ product|productDisplayName }}</strong> modified'),
                [
                    'product' => $product,
                ]
            );
            return $this->redirectToRoute('admin_product_edit', ['id' => $product->getId()]);
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->getFlashMessageSender()->addErrorFlashTwig(t('Please check the correctness of all data filled.'));
        }

        $this->breadcrumb->overrideLastItem(
            new MenuItem(t('Editing product - %name%', ['%name%' => $this->productExtension->getProductDisplayName($product)]))
        );

        $viewParameters = [
            'form' => $form->createView(),
            'product' => $product,
            'productMainCategoriesIndexedByDomainId' => $this->categoryFacade->getProductMainCategoriesIndexedByDomainId($product),
            'domains' => $this->domain->getAll(),
        ];
        if ($product->getPriceCalculationType() === Product::PRICE_CALCULATION_TYPE_AUTO) {
            $viewParameters['productBasePrice'] = $this->adminProductPriceCalculationFacade->calculateProductBasePrice($product);
        }

        try {
            $productSellingPricesIndexedByDomainId = $this->productFacade->getAllProductSellingPricesIndexedByDomainId($product);
            $viewParameters['productSellingPricesIndexedByDomainId'] = $productSellingPricesIndexedByDomainId;
        } catch (\Shopsys\FrameworkBundle\Model\Product\Pricing\Exception\MainVariantPriceCalculationException $ex) {
        }

        return $this->render('@ShopsysFramework/Admin/Content/Product/edit.html.twig', $viewParameters);
    }

    /**
     * @Route("/product/new/")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function newAction(Request $request)
    {
        $productData = $this->productDataFactory->createDefault();

        $form = $this->createForm(ProductEditFormType::class, $productData, ['product' => null]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product = $this->productFacade->create($form->getData());

            $this->getFlashMessageSender()->addSuccessFlashTwig(
                t('Product <strong>{{ product|productDisplayName }}</strong> created'),
                [
                    'product' => $product,
                ]
            );
            return $this->redirectToRoute('admin_product_edit', ['id' => $product->getId()]);
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->getFlashMessageSender()->addErrorFlashTwig(t('Please check the correctness of all data filled.'));
        }

        return $this->render('@ShopsysFramework/Admin/Content/Product/new.html.twig', [
            'form' => $form->createView(),
            'pricingGroupsIndexedByDomainId' => $this->pricingGroupFacade->getAllIndexedByDomainId(),
        ]);
    }

    /**
     * @Route("/product/list/")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function listAction(Request $request)
    {
        $administrator = $this->getUser();
        /* @var $administrator \Shopsys\FrameworkBundle\Model\Administrator\Administrator */

        $advancedSearchForm = $this->advancedSearchFacade->createAdvancedSearchForm($request);
        $advancedSearchData = $advancedSearchForm->getData();
        $quickSearchData = new QuickSearchFormData();

        $quickSearchForm = $this->createForm(QuickSearchFormType::class, $quickSearchData);

        // Cannot call $form->handleRequest() because the GET forms are not handled in POST request.
        // See: https://github.com/symfony/symfony/issues/12244
        $quickSearchForm->submit($request->query->get($quickSearchForm->getName()));

        $massActionForm = $this->createForm(ProductMassActionFormType::class);
        $massActionForm->handleRequest($request);

        $isAdvancedSearchFormSubmitted = $this->advancedSearchFacade->isAdvancedSearchFormSubmitted($request);
        if ($isAdvancedSearchFormSubmitted) {
            $queryBuilder = $this->advancedSearchFacade->getQueryBuilderByAdvancedSearchData($advancedSearchData);
        } else {
            $queryBuilder = $this->productListAdminFacade->getQueryBuilderByQuickSearchData($quickSearchData);
        }

        $grid = $this->getGrid($queryBuilder);

        if ($massActionForm->get('submit')->isClicked()) {
            $this->productMassActionFacade->doMassAction(
                $massActionForm->getData(),
                $queryBuilder,
                array_map('intval', $grid->getSelectedRowIds())
            );

            $this->getFlashMessageSender()->addSuccessFlash(t('Bulk editing done'));

            return $this->redirect($request->headers->get('referer', $this->generateUrl('admin_product_list')));
        }

        $this->administratorGridFacade->restoreAndRememberGridLimit($administrator, $grid);

        return $this->render('@ShopsysFramework/Admin/Content/Product/list.html.twig', [
            'gridView' => $grid->createView(),
            'quickSearchForm' => $quickSearchForm->createView(),
            'advancedSearchForm' => $advancedSearchForm->createView(),
            'massActionForm' => $massActionForm->createView(),
            'isAdvancedSearchFormSubmitted' => $this->advancedSearchFacade->isAdvancedSearchFormSubmitted($request),
        ]);
    }

    /**
     * @Route("/product/delete/{id}", requirements={"id" = "\d+"})
     * @CsrfProtection
     * @param int $id
     */
    public function deleteAction($id)
    {
        try {
            $product = $this->productFacade->getById($id);

            $this->productFacade->delete($id);

            $this->getFlashMessageSender()->addSuccessFlashTwig(
                t('Product <strong>{{ product|productDisplayName }}</strong> deleted'),
                [
                    'product' => $product,
                ]
            );
        } catch (\Shopsys\FrameworkBundle\Model\Product\Exception\ProductNotFoundException $ex) {
            $this->getFlashMessageSender()->addErrorFlash(t('Selected product doesn\'t exist.'));
        }

        return $this->redirectToRoute('admin_product_list');
    }

    /**
     * @Route("/product/get-advanced-search-rule-form/", methods={"post"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function getRuleFormAction(Request $request)
    {
        $ruleForm = $this->advancedSearchFacade->createRuleForm($request->get('filterName'), $request->get('newIndex'));

        return $this->render('@ShopsysFramework/Admin/Content/Product/AdvancedSearch/ruleForm.html.twig', [
            'rulesForm' => $ruleForm->createView(),
        ]);
    }

    /**
     * @Route("/product/create-variant/")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function createVariantAction(Request $request)
    {
        $form = $this->createForm(VariantFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $mainVariant = $formData[VariantFormType::MAIN_VARIANT];
            try {
                $newMainVariant = $this->productVariantFacade->createVariant($mainVariant, $formData[VariantFormType::VARIANTS]);

                $this->getFlashMessageSender()->addSuccessFlashTwig(
                    t('Variant <strong>{{ productVariant|productDisplayName }}</strong> successfully created.'),
                    [
                        'productVariant' => $newMainVariant,
                    ]
                );

                return $this->redirectToRoute('admin_product_edit', ['id' => $newMainVariant->getId()]);
            } catch (\Shopsys\FrameworkBundle\Model\Product\Exception\VariantException $ex) {
                $this->getFlashMessageSender()->addErrorFlash(
                    t('Not possible to create variations of products that are already variant or main variant.')
                );
            }
        }

        return $this->render('@ShopsysFramework/Admin/Content/Product/createVariant.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @return \Shopsys\FrameworkBundle\Component\Grid\Grid
     */
    private function getGrid(QueryBuilder $queryBuilder)
    {
        $dataSource = new QueryBuilderWithRowManipulatorDataSource(
            $queryBuilder,
            'p.id',
            function ($row) {
                $product = $this->productFacade->getById($row['p']['id']);
                $row['product'] = $product;
                return $row;
            }
        );

        $grid = $this->gridFactory->create('productList', $dataSource);
        $grid->enablePaging();
        $grid->enableSelecting();
        $grid->setDefaultOrder('name');

        $grid->addColumn('name', 'pt.name', t('Name'), true);
        $grid->addColumn('price', 'priceForProductList', t('Price'), true)->setClassAttribute('text-right');
        $grid->addColumn('calculatedVisibility', 'p.calculatedVisibility', t('Visibility'))
            ->setClassAttribute('text-center table-col table-col-10');

        $grid->setActionColumnClassAttribute('table-col table-col-10');
        $grid->addEditActionColumn('admin_product_edit', ['id' => 'p.id']);
        $grid->addDeleteActionColumn('admin_product_delete', ['id' => 'p.id'])
            ->setConfirmMessage(t('Do you really want to remove this product?'));

        $grid->setTheme('@ShopsysFramework/Admin/Content/Product/listGrid.html.twig', [
            'VARIANT_TYPE_MAIN' => Product::VARIANT_TYPE_MAIN,
            'VARIANT_TYPE_VARIANT' => Product::VARIANT_TYPE_VARIANT,
        ]);

        return $grid;
    }

    /**
     * @Route("/product/visibility/{productId}")
     * @param int $productId
     */
    public function visibilityAction($productId)
    {
        $product = $this->productFacade->getById($productId);

        return $this->render('@ShopsysFramework/Admin/Content/Product/visibility.html.twig', [
            'product' => $product,
            'domains' => $this->domain->getAll(),
        ]);
    }
}
