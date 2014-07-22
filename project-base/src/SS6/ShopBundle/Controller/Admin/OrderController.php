<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Order\OrderFormData;
use SS6\ShopBundle\Form\Admin\Order\OrderFormType;
use SS6\ShopBundle\Form\Admin\Order\OrderItemFormData;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\PKGrid\PKGrid;
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
		$orderStatusRepository = $this->get('ss6.shop.order.order_status_repository');
		/* @var $orderStatusRepository \SS6\ShopBundle\Model\Order\Status\OrderStatusRepository */
		$orderRepository = $this->get('ss6.shop.order.order_repository');
		/* @var $orderRepository \SS6\ShopBundle\Model\Order\OrderRepository */
		
		$order = $orderRepository->getById($id);
		$allOrderStauses = $orderStatusRepository->findAll();
		$form = $this->createForm(new OrderFormType($allOrderStauses));
		
		try {
			$orderData = new OrderFormData();

			if (!$form->isSubmitted()) {
				

				$customer = $order->getCustomer();
				$customerId = null;
				if ($order->getCustomer() !== null) {
					$customerId = $customer->getId();
				}

				/* @var $order \SS6\ShopBundle\Model\Order\Order */
				$orderData->setId($order->getId());
				$orderData->setOrderNumber($order->getNumber());
				$orderData->setStatusId($order->getStatus()->getId());
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
				$orderData->setPostcode($order->getPostcode());
				$orderData->setDeliveryFirstName($order->getDeliveryFirstName());
				$orderData->setDeliveryLastName($order->getDeliveryLastName());
				$orderData->setDeliveryCompanyName($order->getDeliveryCompanyName());
				$orderData->setDeliveryTelephone($order->getDeliveryTelephone());
				$orderData->setDeliveryStreet($order->getDeliveryStreet());
				$orderData->setDeliveryCity($order->getDeliveryCity());
				$orderData->setDeliveryPostcode($order->getDeliveryPostcode());
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
				return $this->redirect($this->generateUrl('admin_order_list'));
			}
		} catch (\SS6\ShopBundle\Model\Order\Status\Exception\OrderStatusNotFoundException $e) {
			$flashMessage->addError('Zadaný stav objednávky nebyl nalezen, prosím překontrolujte zadané údaje');
		} catch (\SS6\ShopBundle\Model\Customer\Exception\UserNotFoundException $e) {
			$flashMessage->addError('Zadaný zákazník nebyl nalezen, prosím překontrolujte zadané údaje');
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessage->addError('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}
		
		return $this->render('@SS6Shop/Admin/Content/Order/edit.html.twig', array(
			'form' => $form->createView(),
			'order' => $order,
		));
	}

	/**
	 * @Route("/order/list/")
	 */
	public function listAction() {
		$administratorGridFacade = $this->get('ss6.shop.administrator.administrator_grid_facade');
		/* @var $administratorGridFacade \SS6\ShopBundle\Model\Administrator\AdministratorGridFacade */
		$administrator = $this->getUser();
		/* @var $administrator \SS6\ShopBundle\Model\Administrator\Administrator */

		$queryBuilder = $this->getDoctrine()->getManager()->createQueryBuilder();
		/* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
		$queryBuilder
			->select('
				o.id,
				o.number,
				o.createdAt,
				MAX(os.name) AS statusName,
				o.totalPrice,
				(CASE WHEN o.companyName IS NOT NULL
							THEN o.companyName
							ELSE CONCAT(o.firstName, \' \', o.lastName)
						END) AS customerName')
			->from(Order::class, 'o')
			->join('o.status', 'os')
			->groupBy('o.id');

		$grid = new PKGrid(
			'orderList',
			$this->get('request_stack'),
			$this->get('router'),
			$this->get('twig')
		);
		$grid->allowPaging();
		$grid->setDefaultOrder('number');
		$grid->setQueryBuilder($queryBuilder, 'o.id');

		$grid->addColumn('number', 'o.number', 'Č. objednávky', true);
		$grid->addColumn('createdAt', 'o.createdAt', 'Vytvořena', true);
		$grid->addColumn('customerName', 'customerName', 'Zákazník', true);
		$grid->addColumn('statusName', 'statusName', 'Stav', true);
		$grid->addColumn('totalPrice', 'o.totalPrice', 'Celková cena', true)->setClass('text-right');


		$grid->setActionColumnClass('table-col table-col-10');
		$grid->addActionColumn('edit', 'Upravit', 'admin_order_edit', array('id' => 'id'));
		$grid->addActionColumn('delete', 'Smazat', 'admin_order_delete', array('id' => 'id'))
			->setConfirmMessage('Opravdu si přejete objednávku smazat?');

		$administratorGridFacade->restoreAndRememberGridLimit($administrator, $grid);

		return $this->render('@SS6Shop/Admin/Content/Order/list.html.twig', array(
			'gridView' => $grid->createView(),
		));
	}

	/**
	 * @Route("/order/delete/{id}", requirements={"id" = "\d+"})
	 * @param int $id
	 */
	public function deleteAction($id) {
		$flashMessage = $this->get('ss6.shop.flash_message.admin');
		/* @var $flashMessage \SS6\ShopBundle\Model\FlashMessage\FlashMessage */
		$orderRepository = $this->get('ss6.shop.order.order_repository');
		/* @var $orderRepository \SS6\ShopBundle\Model\Order\OrderRepository */

		$orderNumber = $orderRepository->getById($id)->getNumber();
		$orderFacade = $this->get('ss6.shop.order.order_facade');
		/* @var $orderFacade \SS6\ShopBundle\Model\Order\OrderFacade */
		$orderFacade->deleteById($id);
		$flashMessage->addSuccess('Objednávka číslo ' . $orderNumber . ' byl smazána');

		return $this->redirect($this->generateUrl('admin_order_list'));
	}
}
