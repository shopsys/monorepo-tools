<?php

namespace SS6\ShopBundle\Controller\Admin;

use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Column\TextColumn;
use APY\DataGridBundle\Grid\Source\Entity;
use Doctrine\ORM\QueryBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Order\OrderFormData;
use SS6\ShopBundle\Form\Admin\Order\OrderFormType;
use SS6\ShopBundle\Form\Admin\Order\OrderItemFormData;
use SS6\ShopBundle\Model\Order\Order;
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

		$allOrderStauses = $orderStatusRepository->getAll();
		
		$form = $this->createForm(new OrderFormType($allOrderStauses));
		
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
				return $this->redirect($this->generateUrl('admin_order_list'));
			} elseif ($form->isSubmitted()) {
				$flashMessage->addError('Prosím zkontrolujte si správnost vyplnění všech údajů');
				$order = $this->get('ss6.shop.order.order_repository')->getById($id);
			}
		} catch (\SS6\ShopBundle\Model\Order\Status\Exception\OrderStatusNotFoundException $e) {
			$flashMessage->addError('Zadaný stav objednávky nebyl nalezen, prosím překontrolujte zadané údaje');
		} catch (\SS6\ShopBundle\Model\Customer\Exception\UserNotFoundException $e) {
			$flashMessage->addError('Zadaný zákazník nebyl nalezen, prosím překontrolujte zadané údaje');
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
		$source = new Entity(Order::class);

		$tableAlias = $source->getTableAlias();
		$grid = $this->createGrid();
		$source->manipulateQuery(function (QueryBuilder $queryBuilder) use ($tableAlias, $grid) {
			$queryBuilder
				->addSelect(
					'(CASE WHEN ' . $tableAlias . '.companyName IS NOT NULL
							THEN ' . $tableAlias . '.companyName
							ELSE CONCAT(' . $tableAlias . ".firstName, ' ', " . $tableAlias . '.lastName)
						END) AS customerName'
				)
				->add('where', $tableAlias . '.deleted = :deleted')
				->setParameter('deleted', false);

			foreach ($grid->getColumns() as $column) {
				if (!$column->isVisibleForSource() && $column->isSorted()) {
					$queryBuilder->resetDQLPart('orderBy');
					$queryBuilder->orderBy($column->getField(), $column->getOrder());
				}
			}
		});

		$grid->getColumns()->addColumn(new TextColumn(array(
			'id' => 'customerName',
			'type' => 'text',
			'field' => 'customerName',
			'sortable' => true,
			'source' => false,
		)));
		$grid->getColumns()->addColumn(new TextColumn(array(
			'id' => 'statusName',
			'type' => 'text',
			'field' => 'status.name',
			'source' => true,
		)));

		$grid->setSource($source);

		$grid->setVisibleColumns(array('number', 'createdOn', 'customerName', 'statusName', 'totalPrice'));
		$grid->setColumnsOrder(array('number', 'createdOn', 'customerName', 'statusName', 'totalPrice'));
		$grid->setDefaultOrder('createdOn', 'desc');
		$grid->getColumn('number')->setTitle('Č. objednávky');
		$grid->getColumn('createdOn')->setTitle('Vytvořena');
		$grid->getColumn('customerName')->setTitle('Zákazník');
		$grid->getColumn('statusName')->setTitle('Stav');
		$grid->getColumn('totalPrice')->setTitle('Celková cena');

		return $grid->getGridResponse('@SS6Shop/Admin/Content/Order/list.html.twig');
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

		$detailRowAction = new RowAction('Upravit', 'admin_order_edit');
		$detailRowAction->setRouteParameters(array('id'));
		$detailRowAction->setAttributes(array('type' => 'edit'));
		$grid->addRowAction($detailRowAction);

		$deleteRowAction = new RowAction('Smazat', 'admin_order_delete', true);
		$deleteRowAction->setConfirmMessage('Opravdu si přejete objednávku smazat?');
		$deleteRowAction->setRouteParameters(array('id'));
		$deleteRowAction->setAttributes(array('type' => 'delete'));
		$grid->addRowAction($deleteRowAction);

		return $grid;
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
