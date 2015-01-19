<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class OrderStatusController extends Controller {

	/**
	 * @Route("/order_status/list/")
	 */
	public function listAction() {
		$orderStatusInlineEdit = $this->get('ss6.shop.order.status.grid.order_status_inline_edit');
		/* @var $orderStatusInlineEdit \SS6\ShopBundle\Model\Order\Status\Grid\OrderStatusInlineEdit */

		$grid = $orderStatusInlineEdit->getGrid();

		return $this->render('@SS6Shop/Admin/Content/OrderStatus/list.html.twig', [
			'gridView' => $grid->createView(),
		]);
	}

	/**
	 * @Route("/order_status/delete/{id}/{newId}", requirements={"id" = "\d+", "newId" = "\d+"})
	 * @param int $id
	 * @param int|null $newId
	 */
	public function deleteAction($id, $newId = null) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$orderStatusFacade = $this->get('ss6.shop.order.order_status_facade');
		/* @var $orderStatusFacade \SS6\ShopBundle\Model\Order\Status\OrderStatusFacade */

		try {
			$orderStatus = $orderStatusFacade->getById($id);
			$orderStatusFacade->deleteById($id, $newId);

			if ($newId === null) {
				$flashMessageSender->addSuccessFlashTwig('Stav objednávek <strong>{{ name }}</strong> byl smazán', [
					'name' => $orderStatus->getName(),
				]);
			} else {
				$newOrderStatus = $orderStatusFacade->getById($newId);
				$flashMessageSender->addSuccessFlashTwig('Stav objednávek <strong>{{ oldName }}</strong> byl nahrazen stavem'
					. ' <strong>{{ newName }}</strong> a byl smazán.',
					[
						'oldName' => $orderStatus->getName(),
						'newName' => $newOrderStatus->getName(),
					]);
			}
		} catch (\SS6\ShopBundle\Model\Order\Status\Exception\OrderStatusDeletionForbiddenException $e) {
			$flashMessageSender->addErrorFlashTwig('Stav objednávek <strong>{{ name }}</strong>'
					. ' je rezervovaný a nelze jej smazat', [
				'name' => $e->getOrderStatus()->getName(),
			]);
		} catch (\SS6\ShopBundle\Model\Order\Status\Exception\OrderStatusDeletionWithOrdersException $e) {
			$flashMessageSender->addErrorFlashTwig('Stav objednávek <strong>{{ name }}</strong>'
					. ' mají nastaveny některé objednávky, před smazáním jim prosím změňte stav', [
				'name' => $e->getOrderStatus()->getName(),
			]);
		} catch (\SS6\ShopBundle\Model\Order\Status\Exception\OrderStatusNotFoundException $ex) {
			$flashMessageSender->addErrorFlash('Zvolený stav objednávek neexistuje');
		}

		return $this->redirect($this->generateUrl('admin_orderstatus_list'));
	}

	/**
	 * @Route("/order_status/delete_confirm/{id}", requirements={"id" = "\d+"})
	 * @param int $id
	 */
	public function deleteConfirmAction($id) {
		$orderStatusFacade = $this->get('ss6.shop.order.order_status_facade');
		/* @var $orderStatusFacade \SS6\ShopBundle\Model\Order\Status\OrderStatusFacade */
		$confirmDeleteResponseFactory = $this->get('ss6.shop.confirm_delete.confirm_delete_response_factory');
		/* @var $confirmDeleteResponseFactory \SS6\ShopBundle\Model\ConfirmDelete\ConfirmDeleteResponseFactory */;

		try {
			$orderStatus = $orderStatusFacade->getById($id);
			if ($orderStatusFacade->isOrderStatusUsed($orderStatus)) {
				$message = 'Jelikož stav "' . $orderStatus->getName() . '" je používán ještě u některých objednávek, '
					. 'musíte zvolit, jaký stav bude použit místo něj. Jaký stav chcete těmto objednávkám nastavit? '
					. 'Při této změně stavu nebude odeslán email zákazníkům.';
				$ordersStatusNamesById = [];
				foreach ($orderStatusFacade->getAllExceptId($id) as $newOrderStatus) {
					$ordersStatusNamesById[$newOrderStatus->getId()] = $newOrderStatus->getName();
				}
				return $confirmDeleteResponseFactory->createSetNewAndDeleteResponse(
					$message,
					'admin_orderstatus_delete',
					$id,
					$ordersStatusNamesById
				);
			} else {
				$message = 'Opravdu si přejete trvale odstranit stav objednávek "'
					. $orderStatus->getName() . '"? Nikde není použitý.';
				return $confirmDeleteResponseFactory->createDeleteResponse($message, 'admin_orderstatus_delete', $id);
			}
		} catch (\SS6\ShopBundle\Model\Order\Status\Exception\OrderStatusNotFoundException $ex) {
			return new Response('Zvolený stav objednávek neexistuje');
		}
	}

}
