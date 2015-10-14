<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Component\Domain\SelectedDomain;
use SS6\ShopBundle\Component\Grid\GridFactory;
use SS6\ShopBundle\Component\Grid\QueryBuilderDataSource;
use SS6\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;
use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Form\Admin\Customer\CustomerFormType;
use SS6\ShopBundle\Form\Admin\Customer\CustomerFormTypeFactory;
use SS6\ShopBundle\Form\Admin\QuickSearch\QuickSearchFormData;
use SS6\ShopBundle\Form\Admin\QuickSearch\QuickSearchFormType;
use SS6\ShopBundle\Model\Administrator\AdministratorGridFacade;
use SS6\ShopBundle\Model\AdminNavigation\Breadcrumb;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;
use SS6\ShopBundle\Model\Customer\CustomerData;
use SS6\ShopBundle\Model\Customer\CustomerEditFacade;
use SS6\ShopBundle\Model\Customer\CustomerListAdminFacade;
use SS6\ShopBundle\Model\Customer\UserData;
use SS6\ShopBundle\Model\Order\OrderFacade;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade;
use SS6\ShopBundle\Model\Security\LoginAsUserFacade;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class CustomerController extends AdminBaseController {

	const LOGIN_AS_TOKEN_ID_PREFIX = 'loginAs';

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade
	 */
	private $pricingGroupSettingFacade;

	/**
	 * @var \Symfony\Component\Translation\Translator
	 */
	private $translator;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\CustomerListAdminFacade
	 */
	private $customerListAdminFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\CustomerEditFacade
	 */
	private $customerEditFacade;

	/**
	 * @var \SS6\ShopBundle\Form\Admin\Customer\CustomerFormTypeFactory
	 */
	private $customerFormTypeFactory;

	/**
	 * @var \SS6\ShopBundle\Model\AdminNavigation\Breadcrumb
	 */
	private $breadcrumb;

	/**
	 * @var \SS6\ShopBundle\Model\Administrator\AdministratorGridFacade
	 */
	private $administratorGridFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Grid\GridFactory
	 */
	private $gridFactory;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\SelectedDomain
	 */
	private $selectedDomain;

	/**
	 * @var \SS6\ShopBundle\Model\Order\OrderFacade
	 */
	private $orderFacade;

	/**
	 * @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface
	 */
	private $csrfTokenManager;

	/**
	 * @var \SS6\ShopBundle\Model\Security\LoginAsUserFacade
	 */
	private $loginAsUserFacade;

	public function __construct(
		PricingGroupSettingFacade $pricingGroupSettingFacade,
		Translator $translator,
		CustomerListAdminFacade $customerListAdminFacade,
		CustomerEditFacade $customerEditFacade,
		CustomerFormTypeFactory $customerFormTypeFactory,
		Breadcrumb $breadcrumb,
		AdministratorGridFacade $administratorGridFacade,
		GridFactory $gridFactory,
		SelectedDomain $selectedDomain,
		OrderFacade $orderFacade,
		CsrfTokenManagerInterface $csrfTokenManager,
		LoginAsUserFacade $loginAsUserFacade
	) {
		$this->pricingGroupSettingFacade = $pricingGroupSettingFacade;
		$this->translator = $translator;
		$this->customerListAdminFacade = $customerListAdminFacade;
		$this->customerEditFacade = $customerEditFacade;
		$this->customerFormTypeFactory = $customerFormTypeFactory;
		$this->breadcrumb = $breadcrumb;
		$this->administratorGridFacade = $administratorGridFacade;
		$this->gridFactory = $gridFactory;
		$this->selectedDomain = $selectedDomain;
		$this->orderFacade = $orderFacade;
		$this->csrfTokenManager = $csrfTokenManager;
		$this->loginAsUserFacade = $loginAsUserFacade;
	}

	/**
	 * @Route("/customer/edit/{id}", requirements={"id" = "\d+"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 */
	public function editAction(Request $request, $id) {
		$user = $this->customerEditFacade->getUserById($id);
		$form = $this->createForm($this->customerFormTypeFactory->create(CustomerFormType::SCENARIO_EDIT, $user));

		try {
			$customerData = new CustomerData();

			if (!$form->isSubmitted()) {
				$customerData->setFromEntity($user);
			}

			$form->setData($customerData);
			$form->handleRequest($request);

			if ($form->isValid()) {
				$this->transactional(
					function () use ($id, $customerData) {
						$this->customerEditFacade->editByAdmin($id, $customerData);
					}
				);

				$this->getFlashMessageSender()->addSuccessFlashTwig(
					'Byl upraven zákazník <strong><a href="{{ url }}">{{ name }}</a></strong>',
					[
						'name' => $user->getFullName(),
						'url' => $this->generateUrl('admin_customer_edit', ['id' => $user->getId()]),
					]
				);
				return $this->redirectToRoute('admin_customer_list');
			}
		} catch (\SS6\ShopBundle\Model\Customer\Exception\DuplicateEmailException $e) {
			$form->get('email')->addError(new FormError('V databázi se již nachází zákazník s tímto e-mailem'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$this->getFlashMessageSender()->addErrorFlashTwig('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		$this->breadcrumb->replaceLastItem(new MenuItem($this->translator->trans('Editace zákazníka - ') . $user->getFullName()));

		$orders = $this->orderFacade->getCustomerOrderList($user);

		return $this->render('@SS6Shop/Admin/Content/Customer/edit.html.twig', [
			'form' => $form->createView(),
			'user' => $user,
			'orders' => $orders,
			'loginAsUserCsrfToken' => $this->csrfTokenManager->getToken($this->getLoginAsUserCsrfTokenId($id)),
		]);
	}

	/**
	 * @Route("/customer/list/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function listAction(Request $request) {
		$administrator = $this->getUser();
		/* @var $administrator \SS6\ShopBundle\Model\Administrator\Administrator */

		$quickSearchForm = $this->createForm(new QuickSearchFormType());
		$quickSearchForm->setData(new QuickSearchFormData());
		$quickSearchForm->handleRequest($request);
		$quickSearchData = $quickSearchForm->getData();

		$queryBuilder = $this->customerListAdminFacade->getCustomerListQueryBuilderByQuickSearchData(
			$this->selectedDomain->getId(),
			$quickSearchData
		);

		$dataSource = new QueryBuilderDataSource($queryBuilder, 'u.id');

		$grid = $this->gridFactory->create('customerList', $dataSource);
		$grid->enablePaging();
		$grid->setDefaultOrder('name');

		$grid->addColumn('name', 'name', 'Jméno', true);
		$grid->addColumn('city', 'city', 'Město', true);
		$grid->addColumn('telephone', 'telephone', 'Telefon', true);
		$grid->addColumn('email', 'u.email', 'Email', true);
		$grid->addColumn('pricingGroup', 'pricingGroup', 'Cenová skupina', true);
		$grid->addColumn('orders_count', 'ordersCount', 'Počet objednávek', true)->setClassAttribute('text-right');
		$grid->addColumn('orders_sum_price', 'ordersSumPrice', 'Hodnota objednávek', true)
			->setClassAttribute('text-right');
		$grid->addColumn('last_order_at', 'lastOrderAt', 'Poslední objednávka', true)
			->setClassAttribute('text-right');

		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addActionColumn('edit', 'Upravit', 'admin_customer_edit', ['id' => 'id']);
		$grid->addActionColumn('delete', 'Smazat', 'admin_customer_delete', ['id' => 'id'])
			->setConfirmMessage('Opravdu chcete odstranit tohoto zákazníka?');

		$grid->setTheme('@SS6Shop/Admin/Content/Customer/listGrid.html.twig');

		$this->administratorGridFacade->restoreAndRememberGridLimit($administrator, $grid);

		return $this->render('@SS6Shop/Admin/Content/Customer/list.html.twig', [
			'gridView' => $grid->createView(),
			'quickSearchForm' => $quickSearchForm->createView(),
		]);
	}

	/**
	 * @Route("/customer/new/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function newAction(Request $request) {
		$form = $this->createForm(
			$this->customerFormTypeFactory->create(CustomerFormType::SCENARIO_CREATE),
			null,
			['validation_groups' => ['Default', CustomerFormType::SCENARIO_CREATE]]
		);

		try {
			$customerData = new CustomerData();
			$userData = new UserData();
			$defaultPricingGroup = $this->pricingGroupSettingFacade->getDefaultPricingGroupBySelectedDomain();
			$userData->pricingGroup = $defaultPricingGroup;
			$customerData->userData = $userData;

			$form->setData($customerData);
			$form->handleRequest($request);

			if ($form->isValid()) {
				$customerData = $form->getData();

				$user = $this->transactional(
					function () use ($customerData) {
						return $this->customerEditFacade->create($customerData);
					}
				);

				$this->getFlashMessageSender()->addSuccessFlashTwig(
					'Byl vytvořen zákazník <strong><a href="{{ url }}">{{ name }}</a></strong>',
					[
						'name' => $user->getFullName(),
						'url' => $this->generateUrl('admin_customer_edit', ['id' => $user->getId()]),
					]
				);
				return $this->redirectToRoute('admin_customer_list');
			}
		} catch (\SS6\ShopBundle\Model\Customer\Exception\DuplicateEmailException $e) {
			$form->get('userData')->get('email')->addError(new FormError('V databázi se již nachází zákazník s tímto e-mailem'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$this->getFlashMessageSender()->addErrorFlashTwig('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		return $this->render('@SS6Shop/Admin/Content/Customer/new.html.twig', [
			'form' => $form->createView(),
		]);
	}

	/**
	 * @Route("/customer/delete/{id}", requirements={"id" = "\d+"})
	 * @CsrfProtection
	 * @param int $id
	 */
	public function deleteAction($id) {
		try {
			$fullName = $this->customerEditFacade->getUserById($id)->getFullName();
			$this->transactional(
				function () use ($id) {
					$this->customerEditFacade->delete($id);
				}
			);
			$this->getFlashMessageSender()->addSuccessFlashTwig('Zákazník <strong>{{ name }}</strong> byl smazán', [
				'name' => $fullName,
			]);
		} catch (\SS6\ShopBundle\Model\Customer\Exception\UserNotFoundException $ex) {
			$this->getFlashMessageSender()->addErrorFlash('Zvolený zákazník neexistuje');
		}

		return $this->redirectToRoute('admin_customer_list');
	}

	/**
	 * @Route("/customer/login-as-user/{userId}/{csrfToken}/", requirements={"id" = "\d+"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $userId
	 * @param string $csrfToken
	 */
	public function loginAsUserAction(Request $request, $userId, $csrfToken) {
		$csrfTokenId = $this->getLoginAsUserCsrfTokenId($userId);
		if (!$this->isCsrfTokenValid($csrfTokenId, $csrfToken)) {
			$this->getFlashMessageSender()->addErrorFlash('Chyba CSRF, prosím zkuste se přihlásit za uživatele ještě jednou.');
			return $this->redirect($request->server->get('HTTP_REFERER'));
		}

		$user = $this->customerEditFacade->getUserById($userId);
		$this->csrfTokenManager->removeToken($csrfTokenId);
		$this->loginAsUserFacade->rememberLoginAsUser($user);

		return $this->redirectToRoute('front_customer_login_as_remembered_user');
	}

	/**
	 * @param int $userId
	 * @return string
	 */
	private function getLoginAsUserCsrfTokenId($userId) {
		return self::LOGIN_AS_TOKEN_ID_PREFIX . $userId;
	}

}
