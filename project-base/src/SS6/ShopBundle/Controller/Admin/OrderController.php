<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Model\Order\OrderData;
use SS6\ShopBundle\Form\Admin\Order\OrderFormType;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;
use SS6\ShopBundle\Model\Grid\QueryBuilderDataSource;
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
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$orderStatusRepository = $this->get('ss6.shop.order.order_status_repository');
		/* @var $orderStatusRepository \SS6\ShopBundle\Model\Order\Status\OrderStatusRepository */
		$orderRepository = $this->get('ss6.shop.order.order_repository');
		/* @var $orderRepository \SS6\ShopBundle\Model\Order\OrderRepository */
		
		$order = $orderRepository->getById($id);
		$allOrderStatuses = $orderStatusRepository->findAll();
		$form = $this->createForm(new OrderFormType($allOrderStatuses));
		
		try {
			$orderData = new OrderData();

			if (!$form->isSubmitted()) {
				$orderData->setFromEntity($order);
			}
			$form->setData($orderData);
			$form->handleRequest($request);
				
			if ($form->isValid()) {
				$orderFacade = $this->get('ss6.shop.order.order_facade');
				/* @var $orderFacade \SS6\ShopBundle\Model\Order\OrderFacade */

				$order = $orderFacade->edit($id, $orderData);

				$flashMessageSender->addSuccessTwig('Byla upravena objednávka č.'
						. ' <strong><a href="{{ url }}">{{ number }}</a></strong>', array(
					'number' => $order->getNumber(),
					'url' => $this->generateUrl('admin_order_edit', array('id' => $order->getId())),
				));
				return $this->redirect($this->generateUrl('admin_order_list'));
			}
		} catch (\SS6\ShopBundle\Model\Order\Status\Exception\OrderStatusNotFoundException $e) {
			$flashMessageSender->addError('Zadaný stav objednávky nebyl nalezen, prosím překontrolujte zadané údaje');
		} catch (\SS6\ShopBundle\Model\Customer\Exception\UserNotFoundException $e) {
			$flashMessageSender->addError('Zadaný zákazník nebyl nalezen, prosím překontrolujte zadané údaje');
		} catch (\SS6\ShopBundle\Model\Order\Mail\Exception\SendMailFailedException $e) {
			$flashMessageSender->addError('Nepodařilo se odeslat aktualizační email');
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessageSender->addError('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		$breadcrumb = $this->get('ss6.shop.admin_navigation.breadcrumb');
		/* @var $breadcrumb \SS6\ShopBundle\Model\AdminNavigation\Breadcrumb */
		$breadcrumb->replaceLastItem(new MenuItem('Editace objednávky - č. ' . $order->getNumber()));
		
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
		$orderRepository = $this->get('ss6.shop.order.order_repository');
		/* @var $orderRepository \SS6\ShopBundle\Model\Order\OrderRepository */
		$gridFactory = $this->get('ss6.shop.grid.factory');
		/* @var $gridFactory \SS6\ShopBundle\Model\Grid\GridFactory */

		$queryBuilder = $orderRepository->getOrdersListQueryBuilder();
		$queryBuilder
			->select('
				o.id,
				o.number,
				o.domainId AS domainId,
				o.createdAt,
				MAX(os.name) AS statusName,
				o.totalPrice,
				(CASE WHEN o.companyName IS NOT NULL
							THEN o.companyName
							ELSE CONCAT(o.firstName, \' \', o.lastName)
						END) AS customerName')
			->join('o.status', 'os')
			->groupBy('o.id');
		$dataSource = new QueryBuilderDataSource($queryBuilder);

		$grid = $gridFactory->create('orderList', $dataSource);
		$grid->allowPaging();
		$grid->setDefaultOrder('number');

		$grid->addColumn('number', 'o.number', 'Č. objednávky', true);
		$grid->addColumn('created_at', 'o.createdAt', 'Vytvořena', true);
		$grid->addColumn('customer_name', 'customerName', 'Zákazník', true);
		$grid->addColumn('domain_id', 'domainId', 'Doména', true);
		$grid->addColumn('status_name', 'statusName', 'Stav', true);
		$grid->addColumn('total_price', 'o.totalPrice', 'Celková cena', true)->setClassAttribute('text-right');

		$grid->setActionColumnClassAttribute('table-col table-col-10');
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
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$orderRepository = $this->get('ss6.shop.order.order_repository');
		/* @var $orderRepository \SS6\ShopBundle\Model\Order\OrderRepository */

		$orderNumber = $orderRepository->getById($id)->getNumber();
		$orderFacade = $this->get('ss6.shop.order.order_facade');
		/* @var $orderFacade \SS6\ShopBundle\Model\Order\OrderFacade */
		$orderFacade->deleteById($id);

		$flashMessageSender->addSuccessTwig('Objednávka č. <strong>{{ number }}</strong> byla smazána', array(
			'number' => $orderNumber,
		));
		return $this->redirect($this->generateUrl('admin_order_list'));
	}
}
