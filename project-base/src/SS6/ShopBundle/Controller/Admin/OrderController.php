<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Order\OrderFormData;
use SS6\ShopBundle\Form\Admin\Order\OrderFormType;
use SS6\ShopBundle\Form\Admin\Order\OrderItemFormData;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends Controller {
	
	/**
	 * @Route("/order/edit/{id}", requirements={"id" = "\d+"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function editAction(Request $request, $id) {
		$flashMessage = $this->get('ss6.shop.flash_message.admin');
		/* @var $flashMessage \SS6\ShopBundle\Model\FlashMessage\FlashMessage */
		
		$form = $this->createForm(new OrderFormType());
		
		try {
			$orderData = new OrderFormData();

			if (!$form->isSubmitted()) {
				$orderRepository = $this->get('ss6.shop.order.order_repository');
				/* @var $orderRepository \SS6\ShopBundle\Model\Order\OrderRepository */
				$order = $orderRepository->getById($id);

				$customer = $order->getCustomer();
				$customerId = null;
				if ($order->getCustomer() !== null) {
					$customerId = $customer->getId();
				}

				/* @var $order \SS6\ShopBundle\Model\Order\Order */
				$orderData->setId($order->getId());
				$orderData->setOrderNumber($order->getNumber());
				$orderData->setCustomerId($customerId);
				$orderData->setFirstName($order->getFirstName());
				$orderData->setLastName($order->getLastName());
				$orderData->setEmail($order->getEmail());
				$orderData->setTelephone($order->getTelephone());
				$orderData->setCompanyName($order->getCompanyName());
				$orderData->setCompanyNumber($order->getCompanyNumber());
				$orderData->setCompanyTaxNumber($order->getCompanyTaxNumber());
				$orderData->setStreet($order->getStreet());
				$orderData->setCity($order->getCity());
				$orderData->setZip($order->getZip());
				$orderData->setDeliveryFirstName($order->getDeliveryFirstName());
				$orderData->setDeliveryLastName($order->getDeliveryLastName());
				$orderData->setDeliveryCompanyName($order->getDeliveryCompanyName());
				$orderData->setDeliveryTelephone($order->getDeliveryTelephone());
				$orderData->setDeliveryStreet($order->getDeliveryStreet());
				$orderData->setDeliveryCity($order->getDeliveryCity());
				$orderData->setDeliveryZip($order->getDeliveryZip());
				$orderData->setNote($order->getNote());

				$orderItemsData = array();
				foreach ($order->getItems() as $orderItem) {
					$orderItemFormData = new OrderItemFormData();
					$orderItemFormData->setId($orderItem->getId());
					$orderItemFormData->setName($orderItem->getName());
					$orderItemFormData->setPrice($orderItem->getPrice());
					$orderItemFormData->setQuantity($orderItem->getQuantity());
					$orderItemsData[] = $orderItemFormData;
				}
				$orderData->setItems($orderItemsData);
			}
			
			$form->setData($orderData);
			$form->handleRequest($request);
				
			if ($form->isValid()) {
				$orderFacade = $this->get('ss6.shop.order.order_facade');
				/* @var $orderFacade \SS6\ShopBundle\Model\Order\OrderFacade */

				$order = $orderFacade->edit($id, $orderData);

				$flashMessage->addSuccess('Byla upravena objednávka ' . $order->getNumber());
				return $this->redirect($this->generateUrl('admin_order_edit', array('id' => $order->getId())));
			} elseif ($form->isSubmitted()) {
				$flashMessage->addError('Prosím zkontrolujte si správnost vyplnění všech údajů');
				$order = $this->get('ss6.shop.order.order_repository')->getById($id);
			}
		} catch (\SS6\ShopBundle\Model\Customer\Exception\UserNotFoundException $e) {
			$flashMessage->addError('Zadaný zákazník nebyl nalezen, prosím překontrolujte zadané údaje');
		} catch (\SS6\ShopBundle\Model\Order\Exception\OrderNotFoundException $e) {
			throw $this->createNotFoundException($e->getMessage(), $e);
		}
		
		return $this->render('@SS6Shop/Admin/Content/Order/edit.html.twig', array(
			'form' => $form->createView(),
			'order' => $order,
		));
	}
}
