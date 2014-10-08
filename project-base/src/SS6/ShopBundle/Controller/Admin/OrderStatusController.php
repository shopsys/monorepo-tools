<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Order\Status\DeleteFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class OrderStatusController extends Controller {

	/**
	 * @Route("/order_status/list/")
	 */
	public function listAction() {
		$orderStatusInlineEdit = $this->get('ss6.shop.order.status.grid.order_status_inline_edit');
		/* @var $orderStatusInlineEdit \SS6\ShopBundle\Model\Order\Status\Grid\OrderStatusInlineEdit */

		$grid = $orderStatusInlineEdit->getGrid();

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
