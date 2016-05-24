<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Component\Grid\GridFactory;
use SS6\ShopBundle\Component\Grid\QueryBuilderWithRowManipulatorDataSource;
use SS6\ShopBundle\Form\Admin\QuickSearch\QuickSearchFormData;
use SS6\ShopBundle\Form\Admin\QuickSearch\QuickSearchFormType;
use SS6\ShopBundle\Model\Administrator\AdministratorGridFacade;
use SS6\ShopBundle\Model\AdvancedSearch\AdvancedSearchFacade;
use SS6\ShopBundle\Model\Product\Listing\ProductListAdminFacade;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductEditFacade;
use Symfony\Component\HttpFoundation\Request;

class ProductPickerController extends AdminBaseController {

	/**
	 * @var \SS6\ShopBundle\Model\Administrator\AdministratorGridFacade
	 */
	private $administratorGridFacade;

	/**
	 * @var \SS6\ShopBundle\Model\AdvancedSearch\AdvancedSearchFacade
	 */
	private $advancedSearchFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Grid\GridFactory
	 */
	private $gridFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Listing\ProductListAdminFacade
	 */
	private $productListAdminFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductEditFacade
	 */
	private $productEditFacade;

	public function __construct(
		AdministratorGridFacade $administratorGridFacade,
		GridFactory $gridFactory,
		ProductListAdminFacade $productListAdminFacade,
		AdvancedSearchFacade $advancedSearchFacade,
		ProductEditFacade $productEditFacade
	) {
		$this->administratorGridFacade = $administratorGridFacade;
		$this->gridFactory = $gridFactory;
		$this->productListAdminFacade = $productListAdminFacade;
		$this->advancedSearchFacade = $advancedSearchFacade;
		$this->productEditFacade = $productEditFacade;
	}

	/**
	 * @Route("/product-picker/pick-multiple/{jsInstanceId}/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param string $jsInstanceId
	 */
	public function pickMultipleAction(Request $request, $jsInstanceId) {
		return $this->getPickerResponse(
			$request,
			[
				'isMultiple' => true,
			],
			[
				'isMultiple' => true,
				'jsInstanceId' => $jsInstanceId,
				'allowMainVariants' => $request->query->getBoolean('allowMainVariants', true),
				'allowVariants' => $request->query->getBoolean('allowVariants', true),
			]
		);
	}

	/**
	 * @Route("/product-picker/pick-single/{parentInstanceId}/", defaults={"parentInstanceId"="__instance_id__"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param string $parentInstanceId
	 */
	public function pickSingleAction(Request $request, $parentInstanceId) {
		return $this->getPickerResponse(
			$request,
			[
				'isMultiple' => false,
			],
			[
				'isMultiple' => false,
				'parentInstanceId' => $parentInstanceId,
				'allowMainVariants' => $request->query->getBoolean('allowMainVariants', true),
				'allowVariants' => $request->query->getBoolean('allowVariants', true),
			]
		);
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param array	$viewParameters
	 * @param array $gridViewParameters
	 */
	private function getPickerResponse(Request $request, array $viewParameters, array $gridViewParameters) {
		$administrator = $this->getUser();
		/* @var $administrator \SS6\ShopBundle\Model\Administrator\Administrator */

		$advancedSearchForm = $this->advancedSearchFacade->createAdvancedSearchForm($request);
		$advancedSearchData = $advancedSearchForm->getData();

		$quickSearchForm = $this->createForm(new QuickSearchFormType());
		$quickSearchData = new QuickSearchFormData();
		$quickSearchForm->setData($quickSearchData);
		$quickSearchForm->handleRequest($request);

		$isAdvancedSearchFormSubmitted = $this->advancedSearchFacade->isAdvancedSearchFormSubmitted($request);
		if ($isAdvancedSearchFormSubmitted) {
			$queryBuilder = $this->advancedSearchFacade->getQueryBuilderByAdvancedSearchData($advancedSearchData);
		} else {
			$queryBuilder = $this->productListAdminFacade->getQueryBuilderByQuickSearchData($quickSearchData);
		}

		$dataSource = new QueryBuilderWithRowManipulatorDataSource(
			$queryBuilder,
			'p.id',
			function ($row) {
				$product = $this->productEditFacade->getById($row['p']['id']);
				$row['product'] = $product;
				return $row;
			}
		);

		$grid = $this->gridFactory->create('productPicker', $dataSource);
		$grid->enablePaging();
		$grid->setDefaultOrder('name');

		$grid->addColumn('name', 'pt.name', t('Název'), true);
		$grid->addColumn('catnum', 'p.catnum', t('Katalogové číslo'), true);
		$grid->addColumn('calculatedVisibility', 'p.calculatedVisibility', t('Viditelnost'), true)
			->setClassAttribute('table-col table-col-10 text-center');
		$grid->addColumn('select', 'p.id', '')->setClassAttribute('table-col table-col-15 text-center');

		$gridViewParameters['VARIANT_TYPE_MAIN'] = Product::VARIANT_TYPE_MAIN;
		$gridViewParameters['VARIANT_TYPE_VARIANT'] = Product::VARIANT_TYPE_VARIANT;
		$grid->setTheme('@SS6Shop/Admin/Content/ProductPicker/listGrid.html.twig', $gridViewParameters);

		$this->administratorGridFacade->restoreAndRememberGridLimit($administrator, $grid);

		$viewParameters['gridView'] = $grid->createView();
		$viewParameters['quickSearchForm'] = $quickSearchForm->createView();
		$viewParameters['advancedSearchForm'] = $advancedSearchForm->createView();
		$viewParameters['isAdvancedSearchFormSubmitted'] = $this->advancedSearchFacade->isAdvancedSearchFormSubmitted($request);

		return $this->render('@SS6Shop/Admin/Content/ProductPicker/list.html.twig', $viewParameters);
	}

}
