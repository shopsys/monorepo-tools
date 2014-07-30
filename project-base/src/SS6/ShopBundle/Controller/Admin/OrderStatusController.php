<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Order\Status\OrderStatusFormData;
use SS6\ShopBundle\Form\Admin\Order\Status\OrderStatusFormType;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use SS6\ShopBundle\Model\PKGrid\PKGrid;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class OrderStatusController extends Controller {

	/**
	 * @Route("/order_status/new/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function newAction(Request $request) {
		$flashMessage = $this->get('ss6.shop.flash_message.admin');
		/* @var $flashMessage \SS6\ShopBundle\Model\FlashMessage\FlashMessage */

		$form = $this->createForm(new OrderStatusFormType());

		$orderStatusData = new OrderStatusFormData();

		$form->setData($orderStatusData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$orderStatusData = $form->getData();
			$orderStatusFacade = $this->get('ss6.shop.order.order_status_facade');
			/* @var $orderStatusFacade \SS6\ShopBundle\Model\Order\Status\OrderStatusFacade */

			$orderStatus = $orderStatusFacade->create($orderStatusData);

			$flashMessage->addSuccess('Byl vytvořen stav objednávek ' . $orderStatus->getName());
			return $this->redirect($this->generateUrl('admin_orderstatus_list'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessage->addError('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		return $this->render('@SS6Shop/Admin/Content/OrderStatus/new.html.twig', array(
			'form' => $form->createView(),
		));
	}

	/**
	 * @Route("/order_status/edit/{id}", requirements={"id" = "\d+"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 */
	public function editAction(Request $request, $id) {
		$flashMessage = $this->get('ss6.shop.flash_message.admin');
		/* @var $flashMessage \SS6\ShopBundle\Model\FlashMessage\FlashMessage */
		$orderStatusRepository = $this->get('ss6.shop.order.order_status_repository');
		/* @var $orderStatusRepository \SS6\ShopBundle\Model\Order\Status\OrderStatusRepository */

		$orderStatus = $orderStatusRepository->getById($id);
		/* @var $orderStatus \SS6\ShopBundle\Model\Order\Status\OrderStatus */
		$form = $this->createForm(new OrderStatusFormType());
		$orderStatusData = new OrderStatusFormData();

		if (!$form->isSubmitted()) {
			$orderStatusData->setId($orderStatus->getId());
			$orderStatusData->setName($orderStatus->getName());
		}

		$form->setData($orderStatusData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$orderStatusFacade = $this->get('ss6.shop.order.order_status_facade');
			/* @var $orderStatusFacade \SS6\ShopBundle\Model\Order\Status\OrderStatusFacade */

			$orderStatus = $orderStatusFacade->edit($id, $orderStatusData);

			$flashMessage->addSuccess('Byla upraven stav objednávek ' . $orderStatus->getName());
			return $this->redirect($this->generateUrl('admin_orderstatus_list'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessage->addError('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		$breadcrumb = $this->get('ss6.shop.admin_navigation.breadcrumb');
		/* @var $breadcrumb \SS6\ShopBundle\Model\AdminNavigation\Breadcrumb */
		$breadcrumb->replaceLastItem(new MenuItem('Editace stavu objednávek - ' . $orderStatus->getName()));

		return $this->render('@SS6Shop/Admin/Content/OrderStatus/edit.html.twig', array(
			'form' => $form->createView(),
			'orderStatus' => $orderStatus,
		));
	}

	/**
	 * @Route("/order_status/list/")
	 */
	public function listAction() {
		$queryBuilder = $this->getDoctrine()->getManager()->createQueryBuilder();
		$queryBuilder
			->select('os')
			->from(OrderStatus::class, 'os');

		$grid = new PKGrid(
			'orderStatusList',
			$this->get('request_stack'),
			$this->get('router'),
			$this->get('twig')
		);
		$grid->setDefaultOrder('name');
		$grid->setQueryBuilder($queryBuilder);

		$grid->addColumn('name', 'os.name', 'Název', true);

		$grid->setActionColumnClass('table-col table-col-10');
		$grid->addActionColumn('edit', 'Upravit', 'admin_orderstatus_edit', array('id' => 'id'));
		$grid->addActionColumn('delete', 'Smazat', 'admin_orderstatus_delete', array('id' => 'id'))
			->setConfirmMessage('Opravdu si přejete stav objednávky smazat?');

		return $this->render('@SS6Shop/Admin/Content/OrderStatus/list.html.twig', array(
			'gridView' => $grid->createView(),
		));
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
		} catch (\SS6\ShopBundle\Model\Order\Status\Exception\OrderStatusDeletionForbiddenException $e) {
			$flashMessage->addError('Stav objednávek ' . $e->getOrderStatus()->getName() . ' je rezervovaný a nelze jej smazat');
		} catch (\SS6\ShopBundle\Model\Order\Status\Exception\OrderStatusDeletionWithOrdersException $e) {
			$message = 'Stav objednávek ' . $e->getOrderStatus()->getName()
				. ' mají nastaveny některé objednávky, před smazáním jim prosím změňte stav.';
			$flashMessage->addError($message);
		}

		return $this->redirect($this->generateUrl('admin_orderstatus_list'));
	}
}
