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
		$flashMessageTwig = $this->get('ss6.shop.flash_message.twig_sender.admin');
		/* @var $flashMessageTwig \SS6\ShopBundle\Model\FlashMessage\TwigSender */

		$form = $this->createForm(new OrderStatusFormType());

		$orderStatusData = new OrderStatusFormData();

		$form->setData($orderStatusData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$orderStatusData = $form->getData();
			$orderStatusFacade = $this->get('ss6.shop.order.order_status_facade');
			/* @var $orderStatusFacade \SS6\ShopBundle\Model\Order\Status\OrderStatusFacade */

			$orderStatus = $orderStatusFacade->create($orderStatusData);

			$flashMessageTwig->addSuccess('Byl vytvořen stav objednávek'
					. ' <strong><a href="{{ url }}">{{ name }}</a></strong>', array(
				'name' => $orderStatus->getName(),
				'url' => $this->generateUrl('admin_orderstatus_edit', array('id' => $orderStatus->getId())),
			));
			return $this->redirect($this->generateUrl('admin_orderstatus_list'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessageTwig->addError('Prosím zkontrolujte si správnost vyplnění všech údajů');
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
		$flashMessageTwig = $this->get('ss6.shop.flash_message.twig_sender.admin');
		/* @var $flashMessageTwig \SS6\ShopBundle\Model\FlashMessage\TwigSender */
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

			$flashMessageTwig->addSuccess('Byl upraven stav objednávek'
					. ' <strong><a href="{{ url }}">{{ name }}</a></strong>', array(
				'name' => $orderStatus->getName(),
				'url' => $this->generateUrl('admin_orderstatus_edit', array('id' => $orderStatus->getId())),
			));
			return $this->redirect($this->generateUrl('admin_orderstatus_list'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessageTwig->addError('Prosím zkontrolujte si správnost vyplnění všech údajů');
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
		$grid->addActionColumn('edit', 'Upravit', 'admin_orderstatus_edit', array('id' => 'os.id'));
		$grid->addActionColumn('delete', 'Smazat', 'admin_orderstatus_delete', array('id' => 'os.id'))
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
		$flashMessageTwig = $this->get('ss6.shop.flash_message.twig_sender.admin');
		/* @var $flashMessageTwig \SS6\ShopBundle\Model\FlashMessage\TwigSender */
		$orderStatusRepository = $this->get('ss6.shop.order.order_status_repository');
		/* @var $orderStatusRepository \SS6\ShopBundle\Model\Order\Status\OrderStatusRepository */

		try {
			$statusName = $orderStatusRepository->getById($id)->getName();
			$orderStatusFacade = $this->get('ss6.shop.order.order_status_facade');
			/* @var $orderStatusFacade \SS6\ShopBundle\Model\Order\Status\OrderStatusFacade */
			$orderStatusFacade->deleteById($id);

			$flashMessageTwig->addSuccess('Stav objednávek <strong>{{ name }}</strong> byl smazán', array(
				'name' => $statusName,
			));
		} catch (\SS6\ShopBundle\Model\Order\Status\Exception\OrderStatusDeletionForbiddenException $e) {
			$flashMessageTwig->addError('Stav objednávek <strong>{{ name }}</strong>'
					. ' je rezervovaný a nelze jej smazat', array(
				'name' => $e->getOrderStatus()->getName(),
			));
		} catch (\SS6\ShopBundle\Model\Order\Status\Exception\OrderStatusDeletionWithOrdersException $e) {
			$flashMessageTwig->addError('Stav objednávek <strong>{{ name }}</strong>'
					. ' mají nastaveny některé objednávky, před smazáním jim prosím změňte stav', array(
				'name' => $e->getOrderStatus()->getName(),
			));
		}

		return $this->redirect($this->generateUrl('admin_orderstatus_list'));
	}
}
