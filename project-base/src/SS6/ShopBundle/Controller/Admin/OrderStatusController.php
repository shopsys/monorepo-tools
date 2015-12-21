<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\ConfirmDelete\ConfirmDeleteResponseFactory;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;
use SS6\ShopBundle\Model\Order\Status\Grid\OrderStatusInlineEdit;
use SS6\ShopBundle\Model\Order\Status\OrderStatusFacade;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderStatusController extends AdminBaseController {

	/**
	 * @var \SS6\ShopBundle\Component\ConfirmDelete\ConfirmDeleteResponseFactory
	 */
	private $confirmDeleteResponseFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Status\Grid\OrderStatusInlineEdit
	 */
	private $orderStatusInlineEdit;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Status\OrderStatusFacade
	 */
	private $orderStatusFacade;

	public function __construct(
		OrderStatusFacade $orderStatusFacade,
		OrderStatusInlineEdit $orderStatusInlineEdit,
		ConfirmDeleteResponseFactory $confirmDeleteResponseFactory
	) {
		$this->orderStatusFacade = $orderStatusFacade;
		$this->orderStatusInlineEdit = $orderStatusInlineEdit;
		$this->confirmDeleteResponseFactory = $confirmDeleteResponseFactory;
	}

	/**
	 * @Route("/order-status/list/")
	 */
	public function listAction() {
		$grid = $this->orderStatusInlineEdit->getGrid();

		return $this->render('@SS6Shop/Admin/Content/OrderStatus/list.html.twig', [
			'gridView' => $grid->createView(),
		]);
	}

	/**
	 * @Route("/order-status/delete/{id}", requirements={"id" = "\d+"})
	 * @CsrfProtection
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 */
	public function deleteAction(Request $request, $id) {
		$newId = $request->get('newId');

		try {
			$orderStatus = $this->orderStatusFacade->getById($id);
			$this->transactional(
				function () use ($id, $newId) {
					$this->orderStatusFacade->deleteById($id, $newId);
				}
			);

			if ($newId === null) {
				$this->getFlashMessageSender()->addSuccessFlashTwig(
					t('Stav objednávek <strong>{{ name }}</strong> byl smazán'),
					[
						'name' => $orderStatus->getName(),
					]
				);
			} else {
				$newOrderStatus = $this->orderStatusFacade->getById($newId);
				$this->getFlashMessageSender()->addSuccessFlashTwig(
					t('Stav objednávek <strong>{{ oldName }}</strong> byl nahrazen stavem <strong>{{ newName }}</strong> a byl smazán.'),
					[
						'oldName' => $orderStatus->getName(),
						'newName' => $newOrderStatus->getName(),
					]
				);
			}
		} catch (\SS6\ShopBundle\Model\Order\Status\Exception\OrderStatusDeletionForbiddenException $e) {
			$this->getFlashMessageSender()->addErrorFlashTwig(
				t('Stav objednávek <strong>{{ name }}</strong> je rezervovaný a nelze jej smazat'),
				[
					'name' => $e->getOrderStatus()->getName(),
				]
			);
		} catch (\SS6\ShopBundle\Model\Order\Status\Exception\OrderStatusNotFoundException $ex) {
			$this->getFlashMessageSender()->addErrorFlash(t('Zvolený stav objednávek neexistuje'));
		}

		return $this->redirectToRoute('admin_orderstatus_list');
	}

	/**
	 * @Route("/order-status/delete-confirm/{id}", requirements={"id" = "\d+"})
	 * @param int $id
	 */
	public function deleteConfirmAction($id) {
		try {
			$orderStatus = $this->orderStatusFacade->getById($id);
			if ($this->orderStatusFacade->isOrderStatusUsed($orderStatus)) {
				$message = t(
					'Jelikož stav "%name%" je používán ještě u některých objednávek, '
					. 'musíte zvolit, jaký stav bude použit místo něj. Jaký stav chcete těmto objednávkám nastavit? '
					. 'Při této změně stavu nebude odeslán email zákazníkům.',
					['%name%' => $orderStatus->getName()]
				);
				$ordersStatusNamesById = [];
				foreach ($this->orderStatusFacade->getAllExceptId($id) as $newOrderStatus) {
					$ordersStatusNamesById[$newOrderStatus->getId()] = $newOrderStatus->getName();
				}

				return $this->confirmDeleteResponseFactory->createSetNewAndDeleteResponse(
					$message,
					'admin_orderstatus_delete',
					$id,
					$ordersStatusNamesById
				);
			} else {
				$message = t(
					'Opravdu si přejete trvale odstranit stav objednávek "%name%"? Nikde není použitý.',
					['%name%' => $orderStatus->getName()]
				);

				return $this->confirmDeleteResponseFactory->createDeleteResponse($message, 'admin_orderstatus_delete', $id);
			}
		} catch (\SS6\ShopBundle\Model\Order\Status\Exception\OrderStatusNotFoundException $ex) {
			return new Response(t('Zvolený stav objednávek neexistuje'));
		}
	}

}
