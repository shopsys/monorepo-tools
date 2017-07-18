<?php

namespace Shopsys\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\ShopBundle\Component\Controller\AdminBaseController;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Domain\SelectedDomain;
use Shopsys\ShopBundle\Component\Grid\GridFactory;
use Shopsys\ShopBundle\Component\Grid\QueryBuilderDataSource;
use Shopsys\ShopBundle\Component\Router\DomainRouterFactory;
use Shopsys\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;
use Shopsys\ShopBundle\Controller\Admin\LoginController;
use Shopsys\ShopBundle\Form\Admin\Customer\CustomerFormType;
use Shopsys\ShopBundle\Form\Admin\QuickSearch\QuickSearchFormData;
use Shopsys\ShopBundle\Form\Admin\QuickSearch\QuickSearchFormType;
use Shopsys\ShopBundle\Form\ValidationGroup;
use Shopsys\ShopBundle\Model\Administrator\AdministratorGridFacade;
use Shopsys\ShopBundle\Model\AdminNavigation\Breadcrumb;
use Shopsys\ShopBundle\Model\AdminNavigation\MenuItem;
use Shopsys\ShopBundle\Model\Customer\CustomerData;
use Shopsys\ShopBundle\Model\Customer\CustomerFacade;
use Shopsys\ShopBundle\Model\Customer\CustomerListAdminFacade;
use Shopsys\ShopBundle\Model\Customer\User;
use Shopsys\ShopBundle\Model\Customer\UserData;
use Shopsys\ShopBundle\Model\Order\OrderFacade;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade;
use Shopsys\ShopBundle\Model\Security\LoginAsUserFacade;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CustomerController extends AdminBaseController
{
    const LOGIN_AS_TOKEN_ID_PREFIX = 'loginAs';

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade
     */
    private $pricingGroupSettingFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Customer\CustomerListAdminFacade
     */
    private $customerListAdminFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Customer\CustomerFacade
     */
    private $customerFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\AdminNavigation\Breadcrumb
     */
    private $breadcrumb;

    /**
     * @var \Shopsys\ShopBundle\Model\Administrator\AdministratorGridFacade
     */
    private $administratorGridFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Grid\GridFactory
     */
    private $gridFactory;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\SelectedDomain
     */
    private $selectedDomain;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\OrderFacade
     */
    private $orderFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Security\LoginAsUserFacade
     */
    private $loginAsUserFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Router\DomainRouterFactory
     */
    private $domainRouterFactory;

    public function __construct(
        PricingGroupSettingFacade $pricingGroupSettingFacade,
        CustomerListAdminFacade $customerListAdminFacade,
        CustomerFacade $customerFacade,
        Breadcrumb $breadcrumb,
        AdministratorGridFacade $administratorGridFacade,
        GridFactory $gridFactory,
        SelectedDomain $selectedDomain,
        OrderFacade $orderFacade,
        LoginAsUserFacade $loginAsUserFacade,
        DomainRouterFactory $domainRouterFactory
    ) {
        $this->pricingGroupSettingFacade = $pricingGroupSettingFacade;
        $this->customerListAdminFacade = $customerListAdminFacade;
        $this->customerFacade = $customerFacade;
        $this->breadcrumb = $breadcrumb;
        $this->administratorGridFacade = $administratorGridFacade;
        $this->gridFactory = $gridFactory;
        $this->selectedDomain = $selectedDomain;
        $this->orderFacade = $orderFacade;
        $this->loginAsUserFacade = $loginAsUserFacade;
        $this->domainRouterFactory = $domainRouterFactory;
    }

    /**
     * @Route("/customer/edit/{id}", requirements={"id" = "\d+"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $id
     */
    public function editAction(Request $request, $id)
    {
        $user = $this->customerFacade->getUserById($id);
        $customerData = new CustomerData();
        $customerData->setFromEntity($user);

        $form = $this->createForm(CustomerFormType::class, $customerData, [
            'scenario' => CustomerFormType::SCENARIO_EDIT,
            'domain_id' => $this->selectedDomain->getId(),
        ]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            try {
                $this->customerFacade->editByAdmin($id, $customerData);

                $this->getFlashMessageSender()->addSuccessFlashTwig(
                    t('Customer <strong><a href="{{ url }}">{{ name }}</a></strong> modified'),
                    [
                        'name' => $user->getFullName(),
                        'url' => $this->generateUrl('admin_customer_edit', ['id' => $user->getId()]),
                    ]
                );

                return $this->redirectToRoute('admin_customer_list');
            } catch (\Shopsys\ShopBundle\Model\Customer\Exception\DuplicateEmailException $e) {
                $form->get('email')->addError(new FormError(t('There is already a customer with this e-mail in the database')));
            }
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->getFlashMessageSender()->addErrorFlashTwig(t('Please check the correctness of all data filled.'));
        }

        $this->breadcrumb->overrideLastItem(new MenuItem(t('Editing customer - %name%', ['%name%' => $user->getFullName()])));

        $orders = $this->orderFacade->getCustomerOrderList($user);

        return $this->render('@ShopsysShop/Admin/Content/Customer/edit.html.twig', [
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
    public function listAction(Request $request)
    {
        $administrator = $this->getUser();
        /* @var $administrator \Shopsys\ShopBundle\Model\Administrator\Administrator */

        $quickSearchForm = $this->createForm(QuickSearchFormType::class, new QuickSearchFormData());
        $quickSearchForm->handleRequest($request);

        $queryBuilder = $this->customerListAdminFacade->getCustomerListQueryBuilderByQuickSearchData(
            $this->selectedDomain->getId(),
            $quickSearchForm->getData()
        );

        $dataSource = new QueryBuilderDataSource($queryBuilder, 'u.id');

        $grid = $this->gridFactory->create('customerList', $dataSource);
        $grid->enablePaging();
        $grid->setDefaultOrder('name');

        $grid->addColumn('name', 'name', t('Full name'), true);
        $grid->addColumn('city', 'city', t('City'), true);
        $grid->addColumn('telephone', 'telephone', t('Telephone'), true);
        $grid->addColumn('email', 'u.email', t('E-mail'), true);
        $grid->addColumn('pricingGroup', 'pricingGroup', t('Pricing group'), true);
        $grid->addColumn('orders_count', 'ordersCount', t('Number of orders'), true)->setClassAttribute('text-right');
        $grid->addColumn('orders_sum_price', 'ordersSumPrice', t('Orders value'), true)
            ->setClassAttribute('text-right');
        $grid->addColumn('last_order_at', 'lastOrderAt', t('Last order'), true)
            ->setClassAttribute('text-right');

        $grid->setActionColumnClassAttribute('table-col table-col-10');
        $grid->addEditActionColumn('admin_customer_edit', ['id' => 'id']);
        $grid->addDeleteActionColumn('admin_customer_delete', ['id' => 'id'])
            ->setConfirmMessage(t('Do you really want to remove this customer?'));

        $grid->setTheme('@ShopsysShop/Admin/Content/Customer/listGrid.html.twig');

        $this->administratorGridFacade->restoreAndRememberGridLimit($administrator, $grid);

        return $this->render('@ShopsysShop/Admin/Content/Customer/list.html.twig', [
            'gridView' => $grid->createView(),
            'quickSearchForm' => $quickSearchForm->createView(),
        ]);
    }

    /**
     * @Route("/customer/new/")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function newAction(Request $request)
    {
        $customerData = new CustomerData();
        $userData = new UserData();
        $defaultPricingGroup = $this->pricingGroupSettingFacade->getDefaultPricingGroupBySelectedDomain();
        $userData->pricingGroup = $defaultPricingGroup;
        $customerData->userData = $userData;

        $form = $this->createForm(CustomerFormType::class, $customerData, [
            'scenario' => CustomerFormType::SCENARIO_CREATE,
            'domain_id' => $this->selectedDomain->getId(),
        ]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $customerData = $form->getData();

            try {
                $user = $this->customerFacade->create($customerData);

                $this->getFlashMessageSender()->addSuccessFlashTwig(
                    t('Customer <strong><a href="{{ url }}">{{ name }}</a></strong> created'),
                    [
                        'name' => $user->getFullName(),
                        'url' => $this->generateUrl('admin_customer_edit', ['id' => $user->getId()]),
                    ]
                );

                return $this->redirectToRoute('admin_customer_list');
            } catch (\Shopsys\ShopBundle\Model\Customer\Exception\DuplicateEmailException $e) {
                $formErrorMessage = t('There is already a customer with this e-mail in the database');
                $form->get('userData')->get('email')->addError(new FormError($formErrorMessage));
            }
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->getFlashMessageSender()->addErrorFlashTwig(t('Please check the correctness of all data filled.'));
        }

        return $this->render('@ShopsysShop/Admin/Content/Customer/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/customer/delete/{id}", requirements={"id" = "\d+"})
     * @CsrfProtection
     * @param int $id
     */
    public function deleteAction($id)
    {
        try {
            $fullName = $this->customerFacade->getUserById($id)->getFullName();

            $this->customerFacade->delete($id);

            $this->getFlashMessageSender()->addSuccessFlashTwig(
                t('Customer <strong>{{ name }}</strong> deleted'),
                [
                    'name' => $fullName,
                ]
            );
        } catch (\Shopsys\ShopBundle\Model\Customer\Exception\UserNotFoundException $ex) {
            $this->getFlashMessageSender()->addErrorFlash(t('Selected customer doesn\'t exist.'));
        }

        return $this->redirectToRoute('admin_customer_list');
    }

    /**
     * @Route("/customer/login-as-user/{userId}/", requirements={"id" = "\d+"})
     * @param int $userId
     */
    public function loginAsUserAction($userId)
    {
        $user = $this->customerFacade->getUserById($userId);
        $this->loginAsUserFacade->rememberLoginAsUser($user);

        return $this->redirectToRoute('front_customer_login_as_remembered_user');
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Customer\User $user
     * @return string
     */
    private function getSsoLoginAsUserUrl(User $user)
    {
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
