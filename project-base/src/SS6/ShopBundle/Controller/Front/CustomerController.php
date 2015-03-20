<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Form\Front\Customer\CustomerFormType;
use SS6\ShopBundle\Model\Customer\CustomerData;
use SS6\ShopBundle\Model\Security\Roles;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CustomerController extends Controller {

	public function editAction(Request $request) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.front');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */

		if (!$this->isGranted(Roles::ROLE_CUSTOMER)) {
			$flashMessageSender->addErrorFlash('Pro přístup na tuto stránku musíte být přihlášeni');
			return $this->redirect($this->generateUrl('front_login'));
		}

		$user = $this->getUser();

		$form = $this->createForm(new CustomerFormType());

		$customerData = new CustomerData();

		if (!$form->isSubmitted()) {
			$customerData->setFromEntity($user);
		}

		$form->setData($customerData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$customerEditFacade = $this->get('ss6.shop.customer.customer_edit_facade');
			/* @var $customerEditFacade \SS6\ShopBundle\Model\Customer\CustomerEditFacade */

			$customerData = $form->getData();
			$user = $customerEditFacade->editByCustomer(
				$user->getId(),
				$customerData
			);

			$flashMessageSender->addSuccessFlash('Vaše údaje byly úspěšně zaktualizovány');
			return $this->redirect($this->generateUrl('front_customer_edit'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessageSender->addErrorFlash('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		return $this->render('@SS6Shop/Front/Content/Customer/edit.html.twig', [
			'form' => $form->createView(),
		]);
	}

	public function ordersAction() {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.front');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */

		if (!$this->isGranted(Roles::ROLE_CUSTOMER)) {
			$flashMessageSender->addErrorFlash('Pro přístup na tuto stránku musíte být přihlášeni');
			return $this->redirect($this->generateUrl('front_login'));
		}

		$orderFacade = $this->get('ss6.shop.order.order_facade');
		/* @var $orderFacade \SS6\ShopBundle\Model\Order\OrderFacade */
		$user = $this->getUser();
		/* @var $user \SS6\ShopBundle\Model\Customer\User */

		$orders = $orderFacade->getCustomerOrderList($user);
		return $this->render('@SS6Shop/Front/Content/Customer/orders.html.twig', [
			'orders' => $orders,
		]);
	}

	/**
	 * @param string $orderNumber
	 */
	public function orderDetailRegisteredAction($orderNumber) {
		return $this->orderDetailAction(null, $orderNumber);
	}

	/**
	 * @param string $urlHash
	 */
	public function orderDetailUnregisteredAction($urlHash) {
		return $this->orderDetailAction($urlHash, null);
	}

	/**
	 *
	 * @param string $urlHash
	 * @param string $orderNumber
	 */
	private function orderDetailAction($urlHash = null, $orderNumber = null) {
		$orderFacade = $this->get('ss6.shop.order.order_facade');
		/* @var $orderFacade \SS6\ShopBundle\Model\Order\OrderFacade */
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.front');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */

		if ($orderNumber !== null) {
			if (!$this->isGranted(Roles::ROLE_CUSTOMER)) {
				$flashMessageSender->addErrorFlash('Pro přístup na tuto stránku musíte být přihlášeni');
				return $this->redirect($this->generateUrl('front_login'));
			}

			$user = $this->getUser();
			try {
				$order = $orderFacade->getByOrderNumberAndUser($orderNumber, $user);
				/* @var $order \SS6\ShopBundle\Model\Order\Order */
			} catch (\SS6\ShopBundle\Model\Order\Exception\OrderNotFoundException $ex) {
				$flashMessageSender->addErrorFlash('Objednávka nebyla nalezena');
				return $this->redirect($this->generateUrl('front_customer_orders'));
			}
		} else {
			$order = $orderFacade->getByUrlHash($urlHash);
			/* @var $order \SS6\ShopBundle\Model\Order\Order */
		}

		$orderItemPriceCalculation = $this->get('ss6.shop.order.item.order_item_price_calculation');
		/* @var $orderItemPriceCalculation \SS6\ShopBundle\Model\Order\Item\OrderItemPriceCalculation */

		$orderItemTotalPricesById = $orderItemPriceCalculation->calculateTotalPricesIndexedById($order->getItems());

		return $this->render('@SS6Shop/Front/Content/Customer/orderDetail.html.twig', [
			'order' => $order,
			'orderItemTotalPricesById' => $orderItemTotalPricesById,
		]);

	}

}
