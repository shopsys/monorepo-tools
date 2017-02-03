<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Component\Grid\DataSourceInterface;
use SS6\ShopBundle\Component\Grid\GridFactory;
use SS6\ShopBundle\Component\Grid\QueryBuilderWithRowManipulatorDataSource;
use SS6\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;
use SS6\ShopBundle\Form\Admin\Order\OrderFormTypeFactory;
use SS6\ShopBundle\Form\Admin\QuickSearch\QuickSearchFormData;
use SS6\ShopBundle\Form\Admin\QuickSearch\QuickSearchFormType;
use SS6\ShopBundle\Model\Administrator\AdministratorGridFacade;
use SS6\ShopBundle\Model\AdminNavigation\Breadcrumb;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;
use SS6\ShopBundle\Model\AdvancedSearchOrder\AdvancedSearchOrderFacade;
use SS6\ShopBundle\Model\Order\Item\OrderItemFacade;
use SS6\ShopBundle\Model\Order\Item\OrderItemPriceCalculation;
use SS6\ShopBundle\Model\Order\OrderData;
use SS6\ShopBundle\Model\Order\OrderFacade;
use SS6\ShopBundle\Model\Payment\PaymentEditFacade;
use SS6\ShopBundle\Model\Transport\TransportEditFacade;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends AdminBaseController {

	/**
	 * @var \SS6\ShopBundle\Model\AdminNavigation\Breadcrumb
	 */
	private $breadcrumb;

	/**
	 * @var \SS6\ShopBundle\Model\Administrator\AdministratorGridFacade
	 */
	private $administratorGridFacade;

	/**
	 * @var \SS6\ShopBundle\Model\AdvancedSearchOrder\AdvancedSearchOrderFacade
	 */
	private $advancedSearchOrderFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Grid\GridFactory
	 */
	private $gridFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Item\OrderItemPriceCalculation
	 */
	private $orderItemPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Order\OrderFacade
	 */
	private $orderFacade;

	/**
	 * @var \SS6\ShopBundle\Form\Admin\Order\OrderFormTypeFactory
	 */
	private $orderFormTypeFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Item\OrderItemFacade
	 */
	private $orderItemFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\TransportEditFacade
	 */
	private $transportEditFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\PaymentEditFacade
	 */
	private $paymentEditFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
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
					t('Byla upravena objednávka č. <strong><a href="{{ url }}">{{ number }}</a></strong>'),
					[
						'number' => $order->getNumber(),
						'url' => $this->generateUrl('admin_order_edit', ['id' => $order->getId()]),
					]
				);
				return $this->redirectToRoute('admin_order_list');
			} catch (\SS6\ShopBundle\Model\Customer\Exception\UserNotFoundException $e) {
				$this->getFlashMessageSender()->addErrorFlash(
					t('Zadaný zákazník nebyl nalezen, prosím překontrolujte zadané údaje')
				);
			} catch (\SS6\ShopBundle\Model\Mail\Exception\SendMailFailedException $e) {
				$this->getFlashMessageSender()->addErrorFlash(t('Nepodařilo se odeslat aktualizační email'));
			}
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$this->getFlashMessageSender()->addErrorFlash(t('Prosím zkontrolujte si správnost vyplnění všech údajů'));
		}

		$this->breadcrumb->overrideLastItem(new MenuItem(t('Editace objednávky - č. %number%', ['%number%' => $order->getNumber()])));

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
		/* @var $administrator \SS6\ShopBundle\Model\Administrator\Administrator */

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

		$grid->addColumn('preview', 'o.id', t('Náhled'), false);
		$grid->addColumn('number', 'o.number', t('Objednávka č.'), true);
		$grid->addColumn('created_at', 'o.createdAt', t('Vytvořena'), true);
		$grid->addColumn('customer_name', 'customerName', t('Zákazník'), true);
		if ($this->domain->isMultidomain()) {
			$grid->addColumn('domain_id', 'o.domainId', t('Doména'), true);
		}
		$grid->addColumn('status_name', 'statusName', t('Stav'), true);
		$grid->addColumn('total_price', 'o.totalPriceWithVat', t('Celková cena'), false)
			->setClassAttribute('text-right text-no-wrap');

		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addEditActionColumn('admin_order_edit', ['id' => 'id']);
		$grid->addDeleteActionColumn('admin_order_delete', ['id' => 'id'])
			->setConfirmMessage('Opravdu si přejete objednávku smazat?');

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
				t('Objednávka č. <strong>{{ number }}</strong> byla smazána'),
				[
					'number' => $orderNumber,
				]
			);
		} catch (\SS6\ShopBundle\Model\Order\Exception\OrderNotFoundException $ex) {
			$this->getFlashMessageSender()->addErrorFlash(t('Zvolená objednávka neexistuje'));
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
