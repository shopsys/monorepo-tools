<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Component\Domain\SelectedDomain;
use SS6\ShopBundle\Component\Grid\GridFactory;
use SS6\ShopBundle\Component\Grid\QueryBuilderDataSource;
use SS6\ShopBundle\Component\Router\DomainRouterFactory;
use SS6\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;
use SS6\ShopBundle\Controller\Admin\LoginController;
use SS6\ShopBundle\Form\Admin\Customer\CustomerFormType;
use SS6\ShopBundle\Form\Admin\Customer\CustomerFormTypeFactory;
use SS6\ShopBundle\Form\Admin\QuickSearch\QuickSearchFormData;
use SS6\ShopBundle\Form\Admin\QuickSearch\QuickSearchFormType;
use SS6\ShopBundle\Model\Administrator\AdministratorGridFacade;
use SS6\ShopBundle\Model\AdminNavigation\Breadcrumb;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;
use SS6\ShopBundle\Model\Customer\CustomerData;
use SS6\ShopBundle\Model\Customer\CustomerFacade;
use SS6\ShopBundle\Model\Customer\CustomerListAdminFacade;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Customer\UserData;
use SS6\ShopBundle\Model\Order\OrderFacade;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade;
use SS6\ShopBundle\Model\Security\LoginAsUserFacade;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CustomerController extends AdminBaseController {

	const LOGIN_AS_TOKEN_ID_PREFIX = 'loginAs';

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade
	 */
	private $pricingGroupSettingFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\CustomerListAdminFacade
	 */
	private $customerListAdminFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\CustomerFacade
	 */
	private $customerFacade;

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
	 * @var \SS6\ShopBundle\Model\Security\LoginAsUserFacade
	 */
	private $loginAsUserFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Component\Router\DomainRouterFactory
	 */
	private $domainRouterFactory;

	public function __construct(
		PricingGroupSettingFacade $pricingGroupSettingFacade,
		CustomerListAdminFacade $customerListAdminFacade,
		CustomerFacade $customerFacade,
		CustomerFormTypeFactory $customerFormTypeFactory,
		Breadcrumb $breadcrumb,
		AdministratorGridFacade $administratorGridFacade,
		GridFactory $gridFactory,
		SelectedDomain $selectedDomain,
		OrderFacade $orderFacade,
		LoginAsUserFacade $loginAsUserFacade,
		Domain $domain,
		DomainRouterFactory $domainRouterFactory
	) {
		$this->pricingGroupSettingFacade = $pricingGroupSettingFacade;
		$this->customerListAdminFacade = $customerListAdminFacade;
		$this->customerFacade = $customerFacade;
		$this->customerFormTypeFactory = $customerFormTypeFactory;
		$this->breadcrumb = $breadcrumb;
		$this->administratorGridFacade = $administratorGridFacade;
		$this->gridFactory = $gridFactory;
		$this->selectedDomain = $selectedDomain;
		$this->orderFacade = $orderFacade;
		$this->loginAsUserFacade = $loginAsUserFacade;
		$this->domain = $domain;
		$this->domainRouterFactory = $domainRouterFactory;
	}

	/**
	 * @Route("/customer/edit/{id}", requirements={"id" = "\d+"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 */
	public function editAction(Request $request, $id) {
		$user = $this->customerFacade->getUserById($id);
		$form = $this->createForm($this->customerFormTypeFactory->create(CustomerFormType::SCENARIO_EDIT, $user));

		$customerData = new CustomerData();
		$customerData->setFromEntity($user);

		$form->setData($customerData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			try {
				$this->customerFacade->editByAdmin($id, $customerData);

				$this->getFlashMessageSender()->addSuccessFlashTwig(
					t('Byl upraven zákazník <strong><a href="{{ url }}">{{ name }}</a></strong>'),
					[
						'name' => $user->getFullName(),
						'url' => $this->generateUrl('admin_customer_edit', ['id' => $user->getId()]),
					]
				);

				return $this->redirectToRoute('admin_customer_list');
			} catch (\SS6\ShopBundle\Model\Customer\Exception\DuplicateEmailException $e) {
				$form->get('email')->addError(new FormError(t('V databázi se již nachází zákazník s tímto e-mailem')));
			}

		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$this->getFlashMessageSender()->addErrorFlashTwig(t('Prosím zkontrolujte si správnost vyplnění všech údajů'));
		}

		$this->breadcrumb->overrideLastItem(new MenuItem(t('Editace zákazníka - %name%', ['%name%' => $user->getFullName()])));

		$orders = $this->orderFacade->getCustomerOrderList($user);

		return $this->render('@SS6Shop/Admin/Content/Customer/edit.html.twig', [
			'form' => $form->createView(),
			'user' => $user,
			'orders' => $orders,
			'ssoLoginAsUserUrl' => $this->getSsoLoginAsUserUrl($user),
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

		$grid->addColumn('name', 'name', t('Celé jméno'), true);
		$grid->addColumn('city', 'city', t('Město'), true);
		$grid->addColumn('telephone', 'telephone', t('Telefon'), true);
		$grid->addColumn('email', 'u.email', t('E-mail'), true);
		$grid->addColumn('pricingGroup', 'pricingGroup', t('Cenová skupina'), true);
		$grid->addColumn('orders_count', 'ordersCount', t('Počet objednávek'), true)->setClassAttribute('text-right');
		$grid->addColumn('orders_sum_price', 'ordersSumPrice', t('Hodnota objednávek'), true)
			->setClassAttribute('text-right');
		$grid->addColumn('last_order_at', 'lastOrderAt', t('Poslední objednávka'), true)
			->setClassAttribute('text-right');

		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addEditActionColumn('admin_customer_edit', ['id' => 'id']);
		$grid->addDeleteActionColumn('admin_customer_delete', ['id' => 'id'])
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

		$customerData = new CustomerData();
		$userData = new UserData();
		$defaultPricingGroup = $this->pricingGroupSettingFacade->getDefaultPricingGroupBySelectedDomain();
		$userData->pricingGroup = $defaultPricingGroup;
		$customerData->userData = $userData;

		$form->setData($customerData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$customerData = $form->getData();

			try {
				$user = $this->customerFacade->create($customerData);

				$this->getFlashMessageSender()->addSuccessFlashTwig(
					t('Byl vytvořen zákazník <strong><a href="{{ url }}">{{ name }}</a></strong>'),
					[
						'name' => $user->getFullName(),
						'url' => $this->generateUrl('admin_customer_edit', ['id' => $user->getId()]),
					]
				);

				return $this->redirectToRoute('admin_customer_list');
			} catch (\SS6\ShopBundle\Model\Customer\Exception\DuplicateEmailException $e) {
				$form->get('userData')->get('email')->addError(new FormError(t('V databázi se již nachází zákazník s tímto e-mailem')));
			}
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$this->getFlashMessageSender()->addErrorFlashTwig(t('Prosím zkontrolujte si správnost vyplnění všech údajů'));
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
			$fullName = $this->customerFacade->getUserById($id)->getFullName();

			$this->customerFacade->delete($id);

			$this->getFlashMessageSender()->addSuccessFlashTwig(
				t('Zákazník <strong>{{ name }}</strong> byl smazán'),
				[
					'name' => $fullName,
				]
			);
		} catch (\SS6\ShopBundle\Model\Customer\Exception\UserNotFoundException $ex) {
			$this->getFlashMessageSender()->addErrorFlash(t('Zvolený zákazník neexistuje'));
		}

		return $this->redirectToRoute('admin_customer_list');
	}

	/**
	 * @Route("/customer/login-as-user/{userId}/", requirements={"id" = "\d+"})
	 * @param int $userId
	 */
	public function loginAsUserAction($userId) {
		$user = $this->customerFacade->getUserById($userId);
		$this->loginAsUserFacade->rememberLoginAsUser($user);

		return $this->redirectToRoute('front_customer_login_as_remembered_user');
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @return string
	 */
	private function getSsoLoginAsUserUrl(User $user) {
		$customerDomainRouter = $this->domainRouterFactory->getRouter($user->getDomainId());
		$loginAsUserUrl = $customerDomainRouter->generate(
			'admin_customer_loginasuser',
			[
				'userId' => $user->getId(),
			],
			UrlGeneratorInterface::ABSOLUTE_URL
		);

		$mainAdminDomainRouter = $this->domainRouterFactory->getRouter(Domain::MAIN_ADMIN_DOMAIN_ID);
		$ssoLoginAsUserUrl = $mainAdminDomainRouter->generate(
			'admin_login_sso',
			[
				LoginController::ORIGINAL_DOMAIN_ID_PARAMETER_NAME => $user->getDomainId(),
				LoginController::ORIGINAL_REFERER_PARAMETER_NAME => $loginAsUserUrl,
			],
			UrlGeneratorInterface::ABSOLUTE_URL
		);

		return $ssoLoginAsUserUrl;
	}

}
