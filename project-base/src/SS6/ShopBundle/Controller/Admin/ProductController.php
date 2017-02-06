<?php

namespace SS6\ShopBundle\Controller\Admin;

use Doctrine\ORM\QueryBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Component\Grid\GridFactory;
use SS6\ShopBundle\Component\Grid\QueryBuilderWithRowManipulatorDataSource;
use SS6\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;
use SS6\ShopBundle\Form\Admin\Product\ProductEditFormTypeFactory;
use SS6\ShopBundle\Form\Admin\Product\ProductMassActionFormType;
use SS6\ShopBundle\Form\Admin\Product\VariantFormType;
use SS6\ShopBundle\Form\Admin\QuickSearch\QuickSearchFormData;
use SS6\ShopBundle\Form\Admin\QuickSearch\QuickSearchFormType;
use SS6\ShopBundle\Model\Administrator\AdministratorGridFacade;
use SS6\ShopBundle\Model\AdminNavigation\Breadcrumb;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;
use SS6\ShopBundle\Model\AdvancedSearch\AdvancedSearchFacade;
use SS6\ShopBundle\Model\Category\CategoryFacade;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade;
use SS6\ShopBundle\Model\Product\Detail\ProductDetailFactory;
use SS6\ShopBundle\Model\Product\Listing\ProductListAdminFacade;
use SS6\ShopBundle\Model\Product\MassAction\ProductMassActionFacade;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductEditDataFactory;
use SS6\ShopBundle\Model\Product\ProductEditFacade;
use SS6\ShopBundle\Model\Product\ProductVariantFacade;
use SS6\ShopBundle\Twig\ProductExtension;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AdminBaseController {

	/**
	 * @var \SS6\ShopBundle\Model\Category\CategoryFacade
	 */
	private $categoryFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\MassAction\ProductMassActionFacade
	 */
	private $productMassActionFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Grid\GridFactory
	 */
	private $gridFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductEditFacade
	 */
	private $productEditFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Detail\ProductDetailFactory
	 */
	private $productDetailFactory;

	/**
	 * @var \SS6\ShopBundle\Form\Admin\Product\ProductEditFormTypeFactory
	 */
	private $productEditFormTypeFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductEditDataFactory
	 */
	private $productEditDataFactory;

	/**
	 * @var \SS6\ShopBundle\Model\AdminNavigation\Breadcrumb
	 */
	private $breadcrumb;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade
	 */
	private $pricingGroupFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Administrator\AdministratorGridFacade
	 */
	private $administratorGridFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Listing\ProductListAdminFacade
	 */
	private $productListAdminFacade;

	/**
	 * @var \SS6\ShopBundle\Model\AdvancedSearch\AdvancedSearchFacade
	 */
	private $advancedSearchFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductVariantFacade
	 */
	private $productVariantFacade;

	/**
	 * @var \SS6\ShopBundle\Twig\ProductExtension
	 */
	private $productExtension;

	public function __construct(
		CategoryFacade $categoryFacade,
		ProductMassActionFacade $productMassActionFacade,
		GridFactory $gridFactory,
		ProductEditFacade $productEditFacade,
		ProductDetailFactory $productDetailFactory,
		ProductEditFormTypeFactory $productEditFormTypeFactory,
		ProductEditDataFactory $productEditDataFactory,
		Breadcrumb $breadcrumb,
		PricingGroupFacade $pricingGroupFacade,
		AdministratorGridFacade $administratorGridFacade,
		ProductListAdminFacade $productListAdminFacade,
		AdvancedSearchFacade $advancedSearchFacade,
		ProductVariantFacade $productVariantFacade,
		ProductExtension $productExtension
	) {
		$this->categoryFacade = $categoryFacade;
		$this->productMassActionFacade = $productMassActionFacade;
		$this->gridFactory = $gridFactory;
		$this->productEditFacade = $productEditFacade;
		$this->productDetailFactory = $productDetailFactory;
		$this->productEditFormTypeFactory = $productEditFormTypeFactory;
		$this->productEditDataFactory = $productEditDataFactory;
		$this->breadcrumb = $breadcrumb;
		$this->pricingGroupFacade = $pricingGroupFacade;
		$this->administratorGridFacade = $administratorGridFacade;
		$this->productListAdminFacade = $productListAdminFacade;
		$this->advancedSearchFacade = $advancedSearchFacade;
		$this->productVariantFacade = $productVariantFacade;
		$this->productExtension = $productExtension;
	}

	/**
	 * @Route("/product/edit/{id}", requirements={"id" = "\d+"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function editAction(Request $request, $id) {
		$product = $this->productEditFacade->getById($id);

		$form = $this->createForm($this->productEditFormTypeFactory->create($product));
		$productEditData = $this->productEditDataFactory->createFromProduct($product);

		$form->setData($productEditData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$this->productEditFacade->edit($id, $form->getData());

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
			'productDetail' => $this->productDetailFactory->getDetailForProduct($product),
			'productMainCategoriesIndexedByDomainId' => $this->categoryFacade->getProductMainCategoriesIndexedByDomainId($product),
			'PRICE_CALCULATION_TYPE_AUTO' => Product::PRICE_CALCULATION_TYPE_AUTO,
		];

		try {
			$productSellingPricesIndexedByDomainId = $this->productEditFacade->getAllProductSellingPricesIndexedByDomainId($product);
			$viewParameters['productSellingPricesIndexedByDomainId'] = $productSellingPricesIndexedByDomainId;
		} catch (\SS6\ShopBundle\Model\Product\Pricing\Exception\MainVariantPriceCalculationException $ex) {
		}

		return $this->render('@SS6Shop/Admin/Content/Product/edit.html.twig', $viewParameters);
	}

	/**
	 * @Route("/product/new/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function newAction(Request $request) {
		$form = $this->createForm($this->productEditFormTypeFactory->create());

		$productEditData = $this->productEditDataFactory->createDefault();

		$form->setData($productEditData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$product = $this->productEditFacade->create($form->getData());

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

		return $this->render('@SS6Shop/Admin/Content/Product/new.html.twig', [
			'form' => $form->createView(),
			'pricingGroupsIndexedByDomainId' => $this->pricingGroupFacade->getAllIndexedByDomainId(),
		]);
	}

	/**
	 * @Route("/product/list/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function listAction(Request $request) {
		$administrator = $this->getUser();
		/* @var $administrator \SS6\ShopBundle\Model\Administrator\Administrator */

		$advancedSearchForm = $this->advancedSearchFacade->createAdvancedSearchForm($request);
		$advancedSearchData = $advancedSearchForm->getData();

		$quickSearchForm = $this->createForm(new QuickSearchFormType());
		$quickSearchData = new QuickSearchFormData();
		$quickSearchForm->setData($quickSearchData);

		// Cannot call $form->handleRequest() because the GET forms are not handled in POST request.
		// See: https://github.com/symfony/symfony/issues/12244
		$quickSearchForm->submit($request->query->get($quickSearchForm->getName()));

		$massActionForm = $this->createForm(new ProductMassActionFormType());
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

			return $this->redirect($this->getRequest()->headers->get('referer', $this->generateUrl('admin_product_list')));
		}

		$this->administratorGridFacade->restoreAndRememberGridLimit($administrator, $grid);

		return $this->render('@SS6Shop/Admin/Content/Product/list.html.twig', [
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
	public function deleteAction($id) {
		try {
			$product = $this->productEditFacade->getById($id);

			$this->productEditFacade->delete($id);

			$this->getFlashMessageSender()->addSuccessFlashTwig(
				t('Product <strong>{{ product|productDisplayName }}</strong> deleted'),
				[
					'product' => $product,
				]
			);
		} catch (\SS6\ShopBundle\Model\Product\Exception\ProductNotFoundException $ex) {
			$this->getFlashMessageSender()->addErrorFlash(t('Selected product doesn\'t exist.'));
		}

		return $this->redirectToRoute('admin_product_list');
	}

	/**
	 * @Route("/product/get-advanced-search-rule-form/", methods={"post"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function getRuleFormAction(Request $request) {
		$ruleForm = $this->advancedSearchFacade->createRuleForm($request->get('filterName'), $request->get('newIndex'));

		return $this->render('@SS6Shop/Admin/Content/Product/AdvancedSearch/ruleForm.html.twig', [
			'rulesForm' => $ruleForm->createView(),
		]);
	}

	/**
	 * @Route("/product/create-variant/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function createVariantAction(Request $request) {
		$form = $this->createForm(new VariantFormType());

		$form->handleRequest($request);
		if ($form->isValid()) {
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
			} catch (\SS6\ShopBundle\Model\Product\Exception\VariantException $ex) {
				$this->getFlashMessageSender()->addErrorFlash(
					t('Not possible to create variations of products that are already variant or main variant.')
				);
			}
		}

		return $this->render('@SS6Shop/Admin/Content/Product/createVariant.html.twig', [
			'form' => $form->createView(),
		]);
	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $queryBuilder
	 * @return \SS6\ShopBundle\Component\Grid\Grid
	 */
	private function getGrid(QueryBuilder $queryBuilder) {
		$dataSource = new QueryBuilderWithRowManipulatorDataSource(
			$queryBuilder,
			'p.id',
			function ($row) {
				$product = $this->productEditFacade->getById($row['p']['id']);
				$row['product'] = $product;
				return $row;
			}
		);

		$grid = $this->gridFactory->create('productList', $dataSource);
		$grid->enablePaging();
		$grid->enableSelecting();
		$grid->setDefaultOrder('name');

		$grid->addColumn('name', 'pt.name', t('Name'), true);
		$grid->addColumn('price', 'p.price', t('Price'), true)->setClassAttribute('text-right');
		$grid->addColumn('calculatedVisibility', 'p.calculatedVisibility', t('Visibility'))
			->setClassAttribute('text-center table-col table-col-10');

		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addEditActionColumn('admin_product_edit', ['id' => 'p.id']);
		$grid->addDeleteActionColumn('admin_product_delete', ['id' => 'p.id'])
			->setConfirmMessage('Opravdu chcete odstranit toto zboží?');

		$grid->setTheme('@SS6Shop/Admin/Content/Product/listGrid.html.twig', [
			'VARIANT_TYPE_MAIN' => Product::VARIANT_TYPE_MAIN,
			'VARIANT_TYPE_VARIANT' => Product::VARIANT_TYPE_VARIANT,
		]);

		return $grid;
	}

	/**
	 * @Route("/product/visibility/{productId}")
	 * @param int $productId
	 */
	public function visibilityAction($productId) {
		$product = $this->productEditFacade->getById($productId);

		return $this->render('@SS6Shop/Admin/Content/Product/visibility.html.twig', [
			'productDetail' => $this->productDetailFactory->getDetailForProduct($product),
		]);
	}

}
