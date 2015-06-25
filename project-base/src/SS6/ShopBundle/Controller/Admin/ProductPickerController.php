<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\QuickSearch\QuickSearchFormData;
use SS6\ShopBundle\Form\Admin\QuickSearch\QuickSearchFormType;
use SS6\ShopBundle\Model\Administrator\AdministratorGridFacade;
use SS6\ShopBundle\Model\AdvancedSearch\AdvancedSearchFacade;
use SS6\ShopBundle\Model\Grid\GridFactory;
use SS6\ShopBundle\Model\Grid\QueryBuilderDataSource;
use SS6\ShopBundle\Model\Product\Listing\ProductListAdminFacade;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProductPickerController extends Controller {

	/**
	 * @var \SS6\ShopBundle\Model\Administrator\AdministratorGridFacade
	 */
	private $administratorGridFacade;

	/**
	 * @var \SS6\ShopBundle\Model\AdvancedSearch\AdvancedSearchFacade
	 */
	private $advancedSearchFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Grid\GridFactory
	 */
	private $gridFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Listing\ProductListAdminFacade
	 */
	private $productListAdminFacade;

	public function __construct(
		AdministratorGridFacade $administratorGridFacade,
		GridFactory $gridFactory,
		ProductListAdminFacade $productListAdminFacade,
		AdvancedSearchFacade $advancedSearchFacade
	) {
		$this->administratorGridFacade = $administratorGridFacade;
		$this->gridFactory = $gridFactory;
		$this->productListAdminFacade = $productListAdminFacade;
		$this->advancedSearchFacade = $advancedSearchFacade;
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
			]
		);
	}

	/**
	 * @Route("/product-picker/pick-single/{parentInputId}/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param string $parentInputId
	 */
	public function pickSingleAction(Request $request, $parentInputId) {
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

		$dataSource = new QueryBuilderDataSource($queryBuilder, 'p.id');

		$grid = $this->gridFactory->create('productPicker', $dataSource);
		$grid->allowPaging();
		$grid->setDefaultOrder('name');

		$grid->addColumn('visible', 'p.visible', 'Viditelnost', true)->setClassAttribute('table-col table-col-10');
		$grid->addColumn('catnum', 'p.catnum', 'Katalogové číslo', true);
		$grid->addColumn('name', 'pt.name', 'Název', true);
		$grid->addColumn('select', 'p.id', '')->setClassAttribute('table-col table-col-10');

		$grid->setTheme('@SS6Shop/Admin/Content/ProductPicker/listGrid.html.twig', $gridViewParameters);

		$this->administratorGridFacade->restoreAndRememberGridLimit($administrator, $grid);

		$viewParameters['gridView'] = $grid->createView();
		$viewParameters['quickSearchForm'] = $quickSearchForm->createView();
		$viewParameters['advancedSearchForm'] = $advancedSearchForm->createView();
		$viewParameters['isAdvancedSearchFormSubmitted'] = $this->advancedSearchFacade->isAdvancedSearchFormSubmitted($request);

		return $this->render('@SS6Shop/Admin/Content/ProductPicker/list.html.twig', $viewParameters);
	}

}
