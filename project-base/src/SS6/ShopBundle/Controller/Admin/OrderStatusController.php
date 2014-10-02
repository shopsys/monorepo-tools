<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Order\Status\DeleteFormType;
use SS6\ShopBundle\Form\Admin\Order\Status\OrderStatusFormData;
use SS6\ShopBundle\Form\Admin\Order\Status\OrderStatusFormType;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use SS6\ShopBundle\Model\Grid\QueryBuilderDataSource;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class OrderStatusController extends Controller {

	/**
	 * @Route("/order_status/new/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function newAction(Request $request) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */

		$form = $this->createForm(new OrderStatusFormType());

		$orderStatusData = new OrderStatusFormData();

		$form->setData($orderStatusData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$orderStatusData = $form->getData();
			$orderStatusFacade = $this->get('ss6.shop.order.order_status_facade');
			/* @var $orderStatusFacade \SS6\ShopBundle\Model\Order\Status\OrderStatusFacade */

			$orderStatus = $orderStatusFacade->create($orderStatusData);

			$flashMessageSender->addSuccessTwig('Byl vytvořen stav objednávek'
					. ' <strong><a href="{{ url }}">{{ name }}</a></strong>', array(
				'name' => $orderStatus->getName(),
				'url' => $this->generateUrl('admin_orderstatus_edit', array('id' => $orderStatus->getId())),
			));
			return $this->redirect($this->generateUrl('admin_orderstatus_list'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessageSender->addErrorTwig('Prosím zkontrolujte si správnost vyplnění všech údajů');
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
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
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

			$flashMessageSender->addSuccessTwig('Byl upraven stav objednávek'
					. ' <strong><a href="{{ url }}">{{ name }}</a></strong>', array(
				'name' => $orderStatus->getName(),
				'url' => $this->generateUrl('admin_orderstatus_edit', array('id' => $orderStatus->getId())),
			));
			return $this->redirect($this->generateUrl('admin_orderstatus_list'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessageSender->addErrorTwig('Prosím zkontrolujte si správnost vyplnění všech údajů');
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
		$gridFactory = $this->get('ss6.shop.grid.factory');
		/* @var $gridFactory \SS6\ShopBundle\Model\Grid\GridFactory */

		$queryBuilder = $this->getDoctrine()->getManager()->createQueryBuilder();
		$queryBuilder
			->select('os')
			->from(OrderStatus::class, 'os');
		$dataSource = new QueryBuilderDataSource($queryBuilder);

		$grid = $gridFactory->create('orderStatusList', $dataSource);
		$grid->setDefaultOrder('name');

		$grid->addColumn('name', 'os.name', 'Název', true);

		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addActionColumn('edit', 'Upravit', 'admin_orderstatus_edit', array('id' => 'os.id'));
		$grid->addActionColumn('delete', 'Smazat', 'admin_orderstatus_delete', array('id' => 'os.id'));

		return $this->render('@SS6Shop/Admin/Content/OrderStatus/list.html.twig', array(
			'gridView' => $grid->createView(),
		));
	}

	/**
	 * @Route("/order_status/delete/{id}", requirements={"id" = "\d+"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 */
	public function deleteAction(Request $request, $id) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$orderStatusRepository = $this->get('ss6.shop.order.order_status_repository');
		/* @var $orderStatusRepository \SS6\ShopBundle\Model\Order\Status\OrderStatusRepository */
		$orderStatusFacade = $this->get('ss6.shop.order.order_status_facade');
		/* @var $orderStatusFacade \SS6\ShopBundle\Model\Order\Status\OrderStatusFacade */

		try {
			$orderStatus = $orderStatusRepository->getById($id);

			$form = $this->getDeleteForm($id);
			$form->handleRequest($request);

			if ($form->isValid()) {
				$formData = $form->getData();
				$newOrderStatus = $formData['newStatus'];
				/* @var $newOrderStatus \SS6\ShopBundle\Model\Order\Status\OrderStatus */

				$orderStatusFacade->deleteById($id, $formData['newStatus']->getId());

				$flashMessageSender->addSuccessTwig('Stav objednávek <strong>{{ name }}</strong> byl nahrazen stavem '
					. $newOrderStatus->getName() . ' a byl smazán.',
					array(
						'name' => $orderStatus->getName(),
						'newName' => $newOrderStatus->getName(),
					));
			} else {
				$orderStatusFacade->deleteById($id);

				$flashMessageSender->addSuccessTwig('Stav objednávek <strong>{{ name }}</strong> byl smazán', array(
					'name' => $orderStatus->getName(),
				));
			}
		} catch (\SS6\ShopBundle\Model\Order\Status\Exception\OrderStatusDeletionForbiddenException $e) {
			$flashMessageSender->addErrorTwig('Stav objednávek <strong>{{ name }}</strong>'
					. ' je rezervovaný a nelze jej smazat', array(
				'name' => $e->getOrderStatus()->getName(),
			));
		} catch (\SS6\ShopBundle\Model\Order\Status\Exception\OrderStatusDeletionWithOrdersException $e) {
			$flashMessageSender->addErrorTwig('Stav objednávek <strong>{{ name }}</strong>'
					. ' mají nastaveny některé objednávky, před smazáním jim prosím změňte stav', array(
				'name' => $e->getOrderStatus()->getName(),
			));
		}

		return $this->redirect($this->generateUrl('admin_orderstatus_list'));
	}

	/**
	 * @Route("/order_status/delete_ajax_dialog/{id}", requirements={"id" = "\d+"})
	 * @param int $id
	 */
	public function deleteAjaxDialogAction($id) {
		$engine = $this->container->get('templating');

		$orderStatusRepository = $this->get('ss6.shop.order.order_status_repository');
		/* @var $orderStatusRepository \SS6\ShopBundle\Model\Order\Status\OrderStatusRepository */
		$orderRepository = $this->get('ss6.shop.order.order_repository');
		/* @var $orderRepository \SS6\ShopBundle\Model\Order\OrderRepository */

		$orderStatus = $orderStatusRepository->getById($id);
		$ordersCount = $orderRepository->getOrdersCountByStatus($orderStatus);

		$form = $this->getDeleteForm($id);

		$windowId = 'orderStatusDelete';

		if ($ordersCount == 0) {
			return $this->render('@SS6Shop/Front/Inline/jsWindow.html.twig', array(
				'id' => $windowId,
				'text' => 'Opravdu si přejete stav objednávky smazat?',
				'continueButton' => true,
				'continueButtonText' => 'Ano',
				'continueUrl' => $this->generateUrl('admin_orderstatus_delete', array('id' => $id)),
				'closeButton' => true,
			));
		} else {
			$windowHtml = $engine->render('@SS6Shop/Admin/Content/OrderStatus/deleteForm.html.twig', array(
				'orderStatus' => $orderStatus,
				'ordersCount' => $ordersCount,
				'windowId' => $windowId,
				'form' => $form->createView(),
			));

			return $this->render('@SS6Shop/Front/Inline/jsWindow.html.twig', array(
				'id' => $windowId,
				'text' => $windowHtml,
				'noEscape' => true,
			));
		}
	}

	/**
	 * @param int $id
	 * @return \Symfony\Component\Form\Form
	 */
	private function getDeleteForm($id) {
		$orderStatusRepository = $this->get('ss6.shop.order.order_status_repository');
		/* @var $orderStatusRepository \SS6\ShopBundle\Model\Order\Status\OrderStatusRepository */

		$orderStatus = $orderStatusRepository->getById($id);

		$orderStatusesToDelete = $orderStatusRepository->findAllExceptId($orderStatus->getId());

		return $this->createForm(new DeleteFormType($orderStatusesToDelete), null, array(
			'action' => $this->generateUrl('admin_orderstatus_delete', array('id' => $id)),
			'method' => 'GET',
		));
	}

}
