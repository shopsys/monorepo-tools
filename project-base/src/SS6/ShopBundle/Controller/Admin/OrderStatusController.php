<?php

namespace SS6\ShopBundle\Controller\Admin;

use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Source\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class OrderStatusController extends Controller {

	/**
	 * @Route("/order_status/new/{id}", requirements={"id" = "\d+"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 */
	public function newAction(Request $request, $id) {

	}

	/**
	 * @Route("/order_status/edit/{id}", requirements={"id" = "\d+"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 */
	public function editAction(Request $request, $id) {

	}

	/**
	 * @Route("/order_status/list/")
	 */
	public function listAction() {
		$source = new Entity(OrderStatus::class);

		$grid = $this->createGrid();
		$grid->setSource($source);

		$grid->setVisibleColumns(array('name'));
		$grid->setColumnsOrder(array('name'));
		$grid->setDefaultOrder('id', 'asc');
		$grid->getColumn('name')->setTitle('Stav')->setClass('table-col-80');
		
		return $grid->getGridResponse('@SS6Shop/Admin/Content/OrderStatus/list.html.twig');
	}

	/**
	 * @return \APY\DataGridBundle\Grid\Grid
	 */
	private function createGrid() {
		$grid = $this->get('grid');
		/* @var $grid \APY\DataGridBundle\Grid\Grid */

		$grid->hideFilters();
		$grid->setActionsColumnTitle('Akce');
		$grid->setLimits(array(20));
		$grid->setDefaultLimit(20);

		$detailRowAction = new RowAction('Upravit', 'admin_orderstatus_edit');
		$detailRowAction->setRouteParameters(array('id'));
		$grid->addRowAction($detailRowAction);

		$deleteRowAction = new RowAction('Smazat', 'admin_order_delete', true);
		$deleteRowAction->setConfirmMessage('Opravdu si přejete stav objednávky smazat?');
		$deleteRowAction->setRouteParameters(array('id'));
		$deleteRowAction->setAttributes(array('data-action-name' => 'delete'));
		$grid->addRowAction($deleteRowAction);

		return $grid;
	}

	/**
	 * @Route("/order_status/delete/{id}", requirements={"id" = "\d+"})
	 * @param int $id
	 */
	public function deleteAction($id) {
		
	}
}
