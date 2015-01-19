<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Product\QuickSearchFormType;
use SS6\ShopBundle\Model\Grid\QueryBuilderDataSource;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProductPickerController extends Controller {

	/**
	 * @Route("/_product-picker/{parentInputId}/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param string $parentInputId
	 */
	public function listAction(Request $request, $parentInputId) {
		$administratorGridFacade = $this->get('ss6.shop.administrator.administrator_grid_facade');
		/* @var $administratorGridFacade \SS6\ShopBundle\Model\Administrator\AdministratorGridFacade */
		$administrator = $this->getUser();
		/* @var $administrator \SS6\ShopBundle\Model\Administrator\Administrator */
		$gridFactory = $this->get('ss6.shop.grid.factory');
		/* @var $gridFactory \SS6\ShopBundle\Model\Grid\GridFactory */
		$productListAdminFacade = $this->get('ss6.shop.product.list.product_list_admin_facade');
		/* @var $productListAdminFacade \SS6\ShopBundle\Model\Product\Listing\ProductListAdminFacade */

		$form = $this->createForm(new QuickSearchFormType());
		$form->handleRequest($request);
		$searchData = $form->getData();
		$queryBuilder = $productListAdminFacade->getQueryBuilderByQuickSearchData($searchData);
		$dataSource = new QueryBuilderDataSource($queryBuilder, 'p.id');

		$grid = $gridFactory->create('productPicker', $dataSource);
		$grid->allowPaging();
		$grid->setDefaultOrder('name');

		$grid->addColumn('visible', 'p.visible', 'Viditelnost', true)->setClassAttribute('table-col table-col-10');
		$grid->addColumn('catnum', 'p.catnum', 'Katalogové číslo', true);
		$grid->addColumn('name', 'pt.name', 'Název', true);
		$grid->addColumn('select', 'p.id', '')->setClassAttribute('table-col table-col-10');

		$grid->setTheme('@SS6Shop/Admin/Content/ProductPicker/listGrid.html.twig', [
			'parentInputId' => $parentInputId,
		]);

		$administratorGridFacade->restoreAndRememberGridLimit($administrator, $grid);

		return $this->render('@SS6Shop/Admin/Content/ProductPicker/list.html.twig', array(
			'gridView' => $grid->createView(),
			'quickSearchForm' => $form->createView(),
		));
	}

}
