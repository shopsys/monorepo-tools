<?php

namespace Shopsys\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\ShopBundle\Component\Controller\AdminBaseController;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Grid\DataSourceInterface;
use Shopsys\ShopBundle\Component\Grid\GridFactory;
use Shopsys\ShopBundle\Component\Grid\QueryBuilderWithRowManipulatorDataSource;
use Shopsys\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;
use Shopsys\ShopBundle\Form\Admin\Order\OrderFormTypeFactory;
use Shopsys\ShopBundle\Form\Admin\QuickSearch\QuickSearchFormData;
use Shopsys\ShopBundle\Form\Admin\QuickSearch\QuickSearchFormType;
use Shopsys\ShopBundle\Model\Administrator\AdministratorGridFacade;
use Shopsys\ShopBundle\Model\AdminNavigation\Breadcrumb;
use Shopsys\ShopBundle\Model\AdminNavigation\MenuItem;
use Shopsys\ShopBundle\Model\AdvancedSearchOrder\AdvancedSearchOrderFacade;
use Shopsys\ShopBundle\Model\Order\Item\OrderItemFacade;
use Shopsys\ShopBundle\Model\Order\Item\OrderItemPriceCalculation;
use Shopsys\ShopBundle\Model\Order\OrderData;
use Shopsys\ShopBundle\Model\Order\OrderFacade;
use Shopsys\ShopBundle\Model\Payment\PaymentEditFacade;
use Shopsys\ShopBundle\Model\Transport\TransportEditFacade;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends AdminBaseController {

	/**
	 * @var \Shopsys\ShopBundle\Model\AdminNavigation\Breadcrumb
	 */
	private $breadcrumb;

	/**
	 * @var \Shopsys\ShopBundle\Model\Administrator\AdministratorGridFacade
	 */
	private $administratorGridFacade;

	/**
	 * @var \Shopsys\ShopBundle\Model\AdvancedSearchOrder\AdvancedSearchOrderFacade
	 */
	private $advancedSearchOrderFacade;

	/**
	 * @var \Shopsys\ShopBundle\Component\Grid\GridFactory
	 */
	private $gridFactory;

	/**
	 * @var \Shopsys\ShopBundle\Model\Order\Item\OrderItemPriceCalculation
	 */
	private $orderItemPriceCalculation;

	/**
	 * @var \Shopsys\ShopBundle\Model\Order\OrderFacade
	 */
	private $orderFacade;

	/**
	 * @var \Shopsys\ShopBundle\Form\Admin\Order\OrderFormTypeFactory
	 */
	private $orderFormTypeFactory;

	/**
	 * @var \Shopsys\ShopBundle\Model\Order\Item\OrderItemFacade
	 */
	private $orderItemFacade;

	/**
	 * @var \Shopsys\ShopBundle\Model\Transport\TransportEditFacade
	 */
	private $transportEditFacade;

	/**
	 * @var \Shopsys\ShopBundle\Model\Payment\PaymentEditFacade
	 */
	private $paymentEditFacade;

	/**
	 * @var \Shopsys\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	public function __construct(
		OrderFacade $orderFacade,
		AdvancedSearchOrderFacade $advancedSearchOrderFacade,
		OrderItemPriceCalculation $orderItemPriceCalculation,
		AdministratorGridFacade $administratorGridFacade,
		GridFactory $gridFactory,
		OrderFormTypeFactory $orderFormTypeFactory,
		Breadcrumb $breadcrumb,
		OrderItemFacade $orderItemFacade,
		TransportEditFacade $transportEditFacade,
		PaymentEditFacade $paymentEditFacade,
		Domain $domain
	) {
		$this->orderFacade = $orderFacade;
		$this->advancedSearchOrderFacade = $advancedSearchOrderFacade;
		$this->orderItemPriceCalculation = $orderItemPriceCalculation;
		$this->administratorGridFacade = $administratorGridFacade;
		$this->gridFactory = $gridFactory;
		$this->orderFormTypeFactory = $orderFormTypeFactory;
		$this->breadcrumb = $breadcrumb;
		$this->orderItemFacade = $orderItemFacade;
		$this->transportEditFacade = $transportEditFacade;
		$this->paymentEditFacade = $paymentEditFacade;
		$this->domain = $domain;
	}

	/**
	 * @Route("/order/edit/{id}", requirements={"id" = "\d+"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function editAction(Request $request, $id) {
		$order = $this->orderFacade->getById($id);
		$form = $this->createForm($this->orderFormTypeFactory->createForOrder($order));

		$orderData = new OrderData();
		$orderData->setFromEntity($order);

		$form->setData($orderData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			try {
				$order = $this->orderFacade->edit($id, $orderData);

				$this->getFlashMessageSender()->addSuccessFlashTwig(
					t('Order Nr. <strong><a href="{{ url }}">{{ number }}</a></strong> modified'),
					[
						'number' => $order->getNumber(),
						'url' => $this->generateUrl('admin_order_edit', ['id' => $order->getId()]),
					]
				);
				return $this->redirectToRoute('admin_order_list');
			} catch (\Shopsys\ShopBundle\Model\Customer\Exception\UserNotFoundException $e) {
				$this->getFlashMessageSender()->addErrorFlash(
					t('Entered customer not found, please check entered data.')
				);
			} catch (\Shopsys\ShopBundle\Model\Mail\Exception\SendMailFailedException $e) {
				$this->getFlashMessageSender()->addErrorFlash(t('Unable to send updating e-mail'));
			}
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$this->getFlashMessageSender()->addErrorFlash(t('Please check the correctness of all data filled.'));
		}

		$this->breadcrumb->overrideLastItem(new MenuItem(t('Editing order - Nr. %number%', ['%number%' => $order->getNumber()])));

		$orderItemTotalPricesById = $this->orderItemPriceCalculation->calculateTotalPricesIndexedById($order->getItems());

		return $this->render('@SS6Shop/Admin/Content/Order/edit.html.twig', [
			'form' => $form->createView(),
			'order' => $order,
			'orderItemTotalPricesById' => $orderItemTotalPricesById,
			'transportPricesWithVatByTransportId' => $this->transportEditFacade->getTransportPricesWithVatIndexedByTransportId(
				$order->getCurrency()
			),
			'transportVatPercentsByTransportId' => $this->transportEditFacade->getTransportVatPercentsIndexedByTransportId(),
			'paymentPricesWithVatByPaymentId' => $this->paymentEditFacade->getPaymentPricesWithVatIndexedByPaymentId(
				$order->getCurrency()
			),
			'paymentVatPercentsByPaymentId' => $this->paymentEditFacade->getPaymentVatPercentsIndexedByPaymentId(),
		]);
	}

	/**
	 * @Route("/order/add-product/{orderId}", requirements={"orderId" = "\d+"}, condition="request.isXmlHttpRequest()")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $orderId
	 */
	public function addProductAction(Request $request, $orderId) {
		$productId = $request->get('productId');

		$orderItem = $this->orderItemFacade->createOrderProductInOrder($orderId, $productId);

		$order = $this->orderFacade->getById($orderId);

		$orderData = new OrderData();
		$orderData->setFromEntity($order);

		$form = $this->createForm($this->orderFormTypeFactory->createForOrder($order));
		$form->setData($orderData);

		$orderItemTotalPricesById = $this->orderItemPriceCalculation->calculateTotalPricesIndexedById($order->getItems());

		return $this->render('@SS6Shop/Admin/Content/Order/addProduct.html.twig', [
			'form' => $form->createView(),
			'order' => $order,
			'orderItem' => $orderItem,
			'orderItemTotalPricesById' => $orderItemTotalPricesById,
		]);
	}

	/**
	 * @Route("/order/list/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function listAction(Request $request) {
		$administrator = $this->getUser();
		/* @var $administrator \Shopsys\ShopBundle\Model\Administrator\Administrator */

		$advancedSearchForm = $this->advancedSearchOrderFacade->createAdvancedSearchOrderForm($request);
		$advancedSearchData = $advancedSearchForm->getData();

		$quickSearchForm = $this->createForm(new QuickSearchFormType());
		$quickSearchForm->setData(new QuickSearchFormData());
		$quickSearchForm->handleRequest($request);
		$quickSearchData = $quickSearchForm->getData();

		$isAdvancedSearchFormSubmitted = $this->advancedSearchOrderFacade->isAdvancedSearchOrderFormSubmitted($request);
		if ($isAdvancedSearchFormSubmitted) {
			$queryBuilder = $this->advancedSearchOrderFacade->getQueryBuilderByAdvancedSearchOrderData($advancedSearchData);
		} else {
			$queryBuilder = $this->orderFacade->getOrderListQueryBuilderByQuickSearchData($quickSearchData);
		}

		$dataSource = new QueryBuilderWithRowManipulatorDataSource(
			$queryBuilder, 'o.id',
			function ($row) {
				return $this->addOrderEntityToDataSource($row);
			}
		);

		$grid = $this->gridFactory->create('orderList', $dataSource);
		$grid->enablePaging();
		$grid->setDefaultOrder('created_at', DataSourceInterface::ORDER_DESC);

		$grid->addColumn('preview', 'o.id', t('Preview'), false);
		$grid->addColumn('number', 'o.number', t('Order Nr.'), true);
		$grid->addColumn('created_at', 'o.createdAt', t('Created'), true);
		$grid->addColumn('customer_name', 'customerName', t('Customer'), true);
		if ($this->domain->isMultidomain()) {
			$grid->addColumn('domain_id', 'o.domainId', t('Domain'), true);
		}
		$grid->addColumn('status_name', 'statusName', t('Status'), true);
		$grid->addColumn('total_price', 'o.totalPriceWithVat', t('Total price'), false)
			->setClassAttribute('text-right text-no-wrap');

		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addEditActionColumn('admin_order_edit', ['id' => 'id']);
		$grid->addDeleteActionColumn('admin_order_delete', ['id' => 'id'])
			->setConfirmMessage(t('Do you really want to remove the order?'));

		$grid->setTheme('@SS6Shop/Admin/Content/Order/listGrid.html.twig');

		$this->administratorGridFacade->restoreAndRememberGridLimit($administrator, $grid);

		return $this->render('@SS6Shop/Admin/Content/Order/list.html.twig', [
			'gridView' => $grid->createView(),
			'quickSearchForm' => $quickSearchForm->createView(),
			'advancedSearchForm' => $advancedSearchForm->createView(),
			'isAdvancedSearchFormSubmitted' => $this->advancedSearchOrderFacade->isAdvancedSearchOrderFormSubmitted($request),
		]);
	}

	/**
	 * @param array $row
	 * @return array
	 */
	private function addOrderEntityToDataSource(array $row) {
		$row['order'] = $this->orderFacade->getById($row['id']);

		return $row;
	}

	/**
	 * @Route("/order/delete/{id}", requirements={"id" = "\d+"})
	 * @CsrfProtection
	 * @param int $id
	 */
	public function deleteAction($id) {
		try {
			$orderNumber = $this->orderFacade->getById($id)->getNumber();

			$this->orderFacade->deleteById($id);

			$this->getFlashMessageSender()->addSuccessFlashTwig(
				t('Order Nr. <strong>{{ number }}</strong> deleted'),
				[
					'number' => $orderNumber,
				]
			);
		} catch (\Shopsys\ShopBundle\Model\Order\Exception\OrderNotFoundException $ex) {
			$this->getFlashMessageSender()->addErrorFlash(t('Selected order doesn\'t exist.'));
		}

		return $this->redirectToRoute('admin_order_list');
	}

	/**
	 * @Route("/order/get-advanced-search-rule-form/", methods={"post"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function getRuleFormAction(Request $request) {
		$ruleForm = $this->advancedSearchOrderFacade->createRuleForm($request->get('filterName'), $request->get('newIndex'));

		return $this->render('@SS6Shop/Admin/Content/Order/AdvancedSearch/ruleForm.html.twig', [
			'rulesForm' => $ruleForm->createView(),
		]);
	}

	/**
	 * @Route("/order/preview/{id}", requirements={"id" = "\d+"})
	 * @param int $id
	 */
	public function previewAction($id) {
		$order = $this->orderFacade->getById($id);

		return $this->render('@SS6Shop/Admin/Content/Order/preview.html.twig', [
			'order' => $order,
		]);
	}
}
