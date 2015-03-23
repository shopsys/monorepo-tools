<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Product\QuickSearchFormType;
use SS6\ShopBundle\Model\Grid\QueryBuilderDataSource;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProductPickerController extends Controller {

	/**
	 * @Route("/_products-picker/{jsInstanceId}/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param string $jsInstanceId
	 */
	public function productsPickerAction(Request $request, $jsInstanceId) {
		return $this->getPickerResponse(
			$request,
			[
				'isMultiple' => true,
			],
			[
				'isMultiple' => true,
				'jsInstanceId' => $jsInstanceId,
			]
		);
	}

	/**
	 * @Route("/_product-picker/{parentInputId}/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param string $parentInputId
	 */
	public function productPickerAction(Request $request, $parentInputId) {
		return $this->getPickerResponse(
			$request,
			[
				'isMultiple' => false,
			],
			[
				'isMultiple' => false,
				'parentInputId' => $parentInputId,
			]
		);
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param array $viewParameters
	 * @param array $gridViewParameters
	 */
	private function getPickerResponse(Request $request, array $viewParameters, array $gridViewParameters) {
		$administratorGridFacade = $this->get('ss6.shop.administrator.administrator_grid_facade');
		/* @var $administratorGridFacade \SS6\ShopBundle\Model\Administrator\AdministratorGridFacade */
		$administrator = $this->getUser();
		/* @var $administrator \SS6\ShopBundle\Model\Administrator\Administrator */
		$gridFactory = $this->get('ss6.shop.grid.factory');
		/* @var $gridFactory \SS6\ShopBundle\Model\Grid\GridFactory */
		$productListAdminFacade = $this->get('ss6.shop.product.list.product_list_admin_facade');
		/* @var $productListAdminFacade \SS6\ShopBundle\Model\Product\Listing\ProductListAdminFacade */
		$advancedSearchFacade = $this->get('ss6.shop.advanced_search.advanced_search_facade');
		/* @var $advancedSearchFacade \SS6\ShopBundle\Model\AdvancedSearch\AdvancedSearchFacade */

		$advancedSearchForm = $advancedSearchFacade->createAdvancedSearchForm($request);
		$advancedSearchData = $advancedSearchForm->getData();

		$quickSearchForm = $this->createForm(new QuickSearchFormType());
		$quickSearchForm->handleRequest($request);
		$quickSearchData = $quickSearchForm->getData();

		$isAdvancedSearchFormSubmitted = $advancedSearchFacade->isAdvancedSearchFormSubmitted($request);
		if ($isAdvancedSearchFormSubmitted) {
			$queryBuilder = $advancedSearchFacade->getQueryBuilderByAdvancedSearchData($advancedSearchData);
		} else {
			$queryBuilder = $productListAdminFacade->getQueryBuilderByQuickSearchData($quickSearchData);
		}

		$dataSource = new QueryBuilderDataSource($queryBuilder, 'p.id');

		$grid = $gridFactory->create('productPicker', $dataSource);
		$grid->allowPaging();
		$grid->setDefaultOrder('name');

		$grid->addColumn('visible', 'p.visible', 'Viditelnost', true)->setClassAttribute('table-col table-col-10');
		$grid->addColumn('catnum', 'p.catnum', 'Katalogové číslo', true);
		$grid->addColumn('name', 'pt.name', 'Název', true);
		$grid->addColumn('select', 'p.id', '')->setClassAttribute('table-col table-col-10');

		$grid->setTheme('@SS6Shop/Admin/Content/ProductPicker/listGrid.html.twig', $gridViewParameters);

		$administratorGridFacade->restoreAndRememberGridLimit($administrator, $grid);

		$viewParameters['gridView'] = $grid->createView();
		$viewParameters['quickSearchForm'] = $quickSearchForm->createView();
		$viewParameters['advancedSearchForm'] = $advancedSearchForm->createView();
		$viewParameters['isAdvancedSearchFormSubmitted'] = $advancedSearchFacade->isAdvancedSearchFormSubmitted($request);

		return $this->render('@SS6Shop/Admin/Content/ProductPicker/list.html.twig', $viewParameters);
	}

}
