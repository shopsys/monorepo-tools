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

		$deleteRowAction = new RowAction('Smazat', 'admin_orderstatus_delete', true);
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
		$flashMessage = $this->get('ss6.shop.flash_message.admin');
		/* @var $flashMessage \SS6\ShopBundle\Model\FlashMessage\FlashMessage */
		$orderStatusRepository = $this->get('ss6.shop.order.order_status_repository');
		/* @var $orderStatusRepository \SS6\ShopBundle\Model\Order\Status\OrderStatusRepository */

		try {
			$statusName = $orderStatusRepository->getById($id)->getName();
			$orderStatusFacade = $this->get('ss6.shop.order.order_status_facade');
			/* @var $orderStatusFacade \SS6\ShopBundle\Model\Order\Status\OrderStatusFacade */
			$orderStatusFacade->deleteById($id);

			$flashMessage->addSuccess('Stav objednávek ' . $statusName . ' byl smazán');
		} catch (\SS6\ShopBundle\Model\Order\Status\Exception\OrderStatusNotFoundException $e) {
			throw $this->createNotFoundException($e->getMessage(), $e);
		} catch (\SS6\ShopBundle\Model\Order\Status\Exception\DeletionForbiddenOrderStatusException $e) {
			$flashMessage->addError('Stav objednávek ' . $statusName . ' je rezervovaný a nelze jej smazat');
		}

		return $this->redirect($this->generateUrl('admin_orderstatus_list'));
	}
}
