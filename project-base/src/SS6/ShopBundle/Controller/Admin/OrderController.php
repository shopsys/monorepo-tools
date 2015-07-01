<?php

namespace SS6\ShopBundle\Controller\Admin;

use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Controller\Admin\BaseController;
use SS6\ShopBundle\Form\Admin\Order\OrderFormType;
use SS6\ShopBundle\Form\Admin\QuickSearch\QuickSearchFormData;
use SS6\ShopBundle\Form\Admin\QuickSearch\QuickSearchFormType;
use SS6\ShopBundle\Model\Administrator\AdministratorGridFacade;
use SS6\ShopBundle\Model\AdminNavigation\Breadcrumb;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;
use SS6\ShopBundle\Model\AdvancedSearchOrder\AdvancedSearchOrderFacade;
use SS6\ShopBundle\Model\Grid\DataSourceInterface;
use SS6\ShopBundle\Model\Grid\GridFactory;
use SS6\ShopBundle\Model\Grid\QueryBuilderWithRowManipulatorDataSource;
use SS6\ShopBundle\Model\Order\Item\OrderItemPriceCalculation;
use SS6\ShopBundle\Model\Order\OrderData;
use SS6\ShopBundle\Model\Order\OrderFacade;
use SS6\ShopBundle\Model\Order\OrderRepository;
use SS6\ShopBundle\Model\Order\Status\OrderStatusRepository;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends BaseController {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

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
	 * @var \SS6\ShopBundle\Model\Grid\GridFactory
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
	 * @var \SS6\ShopBundle\Model\Order\OrderRepository
	 */
	private $orderRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Status\OrderStatusRepository
	 */
	private $orderStatusRepository;

	/**
	 * @var \Symfony\Component\Translation\Translator
	 */
	private $translator;

	public function __construct(
		OrderFacade $orderFacade,
		AdvancedSearchOrderFacade $advancedSearchOrderFacade,
		OrderStatusRepository $orderStatusRepository,
		Translator $translator,
		OrderRepository $orderRepository,
		OrderItemPriceCalculation $orderItemPriceCalculation,
		AdministratorGridFacade $administratorGridFacade,
		GridFactory $gridFactory,
		Breadcrumb $breadcrumb,
		EntityManager $em
	) {
		$this->orderFacade = $orderFacade;
		$this->advancedSearchOrderFacade = $advancedSearchOrderFacade;
		$this->orderStatusRepository = $orderStatusRepository;
		$this->translator = $translator;
		$this->orderRepository = $orderRepository;
		$this->orderItemPriceCalculation = $orderItemPriceCalculation;
		$this->administratorGridFacade = $administratorGridFacade;
		$this->gridFactory = $gridFactory;
		$this->breadcrumb = $breadcrumb;
		$this->em = $em;
	}

	/**
	 * @Route("/order/edit/{id}", requirements={"id" = "\d+"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function editAction(Request $request, $id) {
		$order = $this->orderRepository->getById($id);
		$allOrderStatuses = $this->orderStatusRepository->findAll();
		$form = $this->createForm(new OrderFormType($allOrderStatuses));

		try {
			$orderData = new OrderData();
			$orderData->setFromEntity($order);

			$form->setData($orderData);
			$form->handleRequest($request);

			if ($form->isValid()) {
				$order = $this->em->transactional(
					function () use ($id, $orderData) {
						return $this->orderFacade->edit($id, $orderData);
					}
				);

				$this->getFlashMessageSender()->addSuccessFlashTwig('Byla upravena objednávka č.'
						. ' <strong><a href="{{ url }}">{{ number }}</a></strong>', [
					'number' => $order->getNumber(),
					'url' => $this->generateUrl('admin_order_edit', ['id' => $order->getId()]),
				]);
				return $this->redirect($this->generateUrl('admin_order_list'));
			}
		} catch (\SS6\ShopBundle\Model\Order\Status\Exception\OrderStatusNotFoundException $e) {
			$this->getFlashMessageSender()->addErrorFlash('Zadaný stav objednávky nebyl nalezen, prosím překontrolujte zadané údaje');
		} catch (\SS6\ShopBundle\Model\Customer\Exception\UserNotFoundException $e) {
			$this->getFlashMessageSender()->addErrorFlash('Zadaný zákazník nebyl nalezen, prosím překontrolujte zadané údaje');
		} catch (\SS6\ShopBundle\Model\Mail\Exception\SendMailFailedException $e) {
			$this->getFlashMessageSender()->addErrorFlash('Nepodařilo se odeslat aktualizační email');
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$this->getFlashMessageSender()->addErrorFlash('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		$this->breadcrumb->replaceLastItem(new MenuItem($this->translator->trans('Editace objednávky - č. ') . $order->getNumber()));

		$orderItemTotalPricesById = $this->orderItemPriceCalculation->calculateTotalPricesIndexedById($order->getItems());

		return $this->render('@SS6Shop/Admin/Content/Order/edit.html.twig', [
			'form' => $form->createView(),
			'order' => $order,
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
		$grid->allowPaging();
		$grid->setDefaultOrder('created_at', DataSourceInterface::ORDER_DESC);

		$grid->addColumn('preview', 'o.id', 'Náhled', false);
		$grid->addColumn('number', 'o.number', 'Č. objednávky', true);
		$grid->addColumn('created_at', 'o.createdAt', 'Vytvořena', true);
		$grid->addColumn('customer_name', 'customerName', 'Zákazník', true);
		$grid->addColumn('domain_id', 'o.domainId', 'Doména', true);
		$grid->addColumn('status_name', 'statusName', 'Stav', true);
		$grid->addColumn('total_price', 'o.totalPriceWithVat', 'Celková cena', false)->setClassAttribute('text-right text-nowrap');

		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addActionColumn('edit', 'Upravit', 'admin_order_edit', ['id' => 'id']);
		$grid->addActionColumn('delete', 'Smazat', 'admin_order_delete', ['id' => 'id'])
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
	 * @param int $id
	 */
	public function deleteAction($id) {
		try {
			$orderNumber = $this->orderRepository->getById($id)->getNumber();
			$this->em->transactional(
				function () use ($id) {
					$this->orderFacade->deleteById($id);
				}
			);

			$this->getFlashMessageSender()->addSuccessFlashTwig('Objednávka č. <strong>{{ number }}</strong> byla smazána', [
				'number' => $orderNumber,
			]);
		} catch (\SS6\ShopBundle\Model\Order\Exception\OrderNotFoundException $ex) {
			$this->getFlashMessageSender()->addErrorFlash('Zvolená objednávka neexistuje');
		}

		return $this->redirect($this->generateUrl('admin_order_list'));
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
