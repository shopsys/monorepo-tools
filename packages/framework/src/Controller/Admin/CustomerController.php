<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Grid\GridFactory;
use Shopsys\FrameworkBundle\Component\Grid\QueryBuilderDataSource;
use Shopsys\FrameworkBundle\Component\Router\DomainRouterFactory;
use Shopsys\FrameworkBundle\Component\Router\Security\Annotation\CsrfProtection;
use Shopsys\FrameworkBundle\Form\Admin\Customer\CustomerFormType;
use Shopsys\FrameworkBundle\Form\Admin\QuickSearch\QuickSearchFormData;
use Shopsys\FrameworkBundle\Form\Admin\QuickSearch\QuickSearchFormType;
use Shopsys\FrameworkBundle\Model\Administrator\AdministratorGridFacade;
use Shopsys\FrameworkBundle\Model\AdminNavigation\Breadcrumb;
use Shopsys\FrameworkBundle\Model\AdminNavigation\MenuItem;
use Shopsys\FrameworkBundle\Model\Customer\CustomerData;
use Shopsys\FrameworkBundle\Model\Customer\CustomerFacade;
use Shopsys\FrameworkBundle\Model\Customer\CustomerListAdminFacade;
use Shopsys\FrameworkBundle\Model\Customer\User;
use Shopsys\FrameworkBundle\Model\Customer\UserDataFactory;
use Shopsys\FrameworkBundle\Model\Order\OrderFacade;
use Shopsys\FrameworkBundle\Model\Security\LoginAsUserFacade;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CustomerController extends AdminBaseController
{
    const LOGIN_AS_TOKEN_ID_PREFIX = 'loginAs';

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\UserDataFactory
     */
    private $userDataFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\CustomerListAdminFacade
     */
    private $customerListAdminFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\CustomerFacade
     */
    private $customerFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\AdminNavigation\Breadcrumb
     */
    private $breadcrumb;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Administrator\AdministratorGridFacade
     */
    private $administratorGridFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Grid\GridFactory
     */
    private $gridFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade
     */
    private $adminDomainTabsFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\OrderFacade
     */
    private $orderFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Security\LoginAsUserFacade
     */
    private $loginAsUserFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\DomainRouterFactory
     */
    private $domainRouterFactory;

    public function __construct(
        UserDataFactory $userDataFactory,
        CustomerListAdminFacade $customerListAdminFacade,
        CustomerFacade $customerFacade,
        Breadcrumb $breadcrumb,
        AdministratorGridFacade $administratorGridFacade,
        GridFactory $gridFactory,
        AdminDomainTabsFacade $adminDomainTabsFacade,
        OrderFacade $orderFacade,
        LoginAsUserFacade $loginAsUserFacade,
        DomainRouterFactory $domainRouterFactory
    ) {
        $this->userDataFactory = $userDataFactory;
        $this->customerListAdminFacade = $customerListAdminFacade;
        $this->customerFacade = $customerFacade;
        $this->breadcrumb = $breadcrumb;
        $this->administratorGridFacade = $administratorGridFacade;
        $this->gridFactory = $gridFactory;
        $this->adminDomainTabsFacade = $adminDomainTabsFacade;
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
            'user' => $user,
            'domain_id' => $this->adminDomainTabsFacade->getSelectedDomainId(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->customerFacade->editByAdmin($id, $customerData);

            $this->getFlashMessageSender()->addSuccessFlashTwig(
                t('Customer <strong><a href="{{ url }}">{{ name }}</a></strong> modified'),
                [
                    'name' => $user->getFullName(),
                    'url' => $this->generateUrl('admin_customer_edit', ['id' => $user->getId()]),
                ]
            );

            return $this->redirectToRoute('admin_customer_list');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->getFlashMessageSender()->addErrorFlashTwig(t('Please check the correctness of all data filled.'));
        }

        $this->breadcrumb->overrideLastItem(new MenuItem(t('Editing customer - %name%', ['%name%' => $user->getFullName()])));

        $orders = $this->orderFacade->getCustomerOrderList($user);

        return $this->render('@ShopsysFramework/Admin/Content/Customer/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'orders' => $orders,
            'ssoLoginAsUserUrl' => $this->getSsoLoginAsUserUrl($user),
        ]);
    }

    /**
     * @Route("/customer/list/")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function listAction(Request $request)
    {
        $administrator = $this->getUser();
        /* @var $administrator \Shopsys\FrameworkBundle\Model\Administrator\Administrator */

        $quickSearchForm = $this->createForm(QuickSearchFormType::class, new QuickSearchFormData());
        $quickSearchForm->handleRequest($request);

        $queryBuilder = $this->customerListAdminFacade->getCustomerListQueryBuilderByQuickSearchData(
            $this->adminDomainTabsFacade->getSelectedDomainId(),
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

        $grid->setTheme('@ShopsysFramework/Admin/Content/Customer/listGrid.html.twig');

        $this->administratorGridFacade->restoreAndRememberGridLimit($administrator, $grid);

        return $this->render('@ShopsysFramework/Admin/Content/Customer/list.html.twig', [
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
        $selectedDomainId = $this->adminDomainTabsFacade->getSelectedDomainId();
        $userData = $this->userDataFactory->createDefault($selectedDomainId);
        $customerData->userData = $userData;

        $form = $this->createForm(CustomerFormType::class, $customerData, [
            'user' => null,
            'domain_id' => $selectedDomainId,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $customerData = $form->getData();
            $user = $this->customerFacade->create($customerData);

            $this->getFlashMessageSender()->addSuccessFlashTwig(
                t('Customer <strong><a href="{{ url }}">{{ name }}</a></strong> created'),
                [
                    'name' => $user->getFullName(),
                    'url' => $this->generateUrl('admin_customer_edit', ['id' => $user->getId()]),
                ]
            );

            return $this->redirectToRoute('admin_customer_list');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->getFlashMessageSender()->addErrorFlashTwig(t('Please check the correctness of all data filled.'));
        }

        return $this->render('@ShopsysFramework/Admin/Content/Customer/new.html.twig', [
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
        } catch (\Shopsys\FrameworkBundle\Model\Customer\Exception\UserNotFoundException $ex) {
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
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $user
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
