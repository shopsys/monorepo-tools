<?php

namespace Shopsys\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\ShopBundle\Component\Controller\AdminBaseController;
use Shopsys\ShopBundle\Component\Grid\GridFactory;
use Shopsys\ShopBundle\Component\Grid\QueryBuilderDataSource;
use Shopsys\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;
use Shopsys\ShopBundle\Form\Admin\Administrator\AdministratorFormType;
use Shopsys\ShopBundle\Model\Administrator\Activity\AdministratorActivityFacade;
use Shopsys\ShopBundle\Model\Administrator\AdministratorData;
use Shopsys\ShopBundle\Model\Administrator\AdministratorFacade;
use Shopsys\ShopBundle\Model\AdminNavigation\Breadcrumb;
use Shopsys\ShopBundle\Model\AdminNavigation\MenuItem;
use Symfony\Component\HttpFoundation\Request;

class AdministratorController extends AdminBaseController
{
    const MAX_ADMINISTRATOR_ACTIVITIES_COUNT = 10;

    /**
     * @var \Shopsys\ShopBundle\Model\AdminNavigation\Breadcrumb
     */
    private $breadcrumb;

    /**
     * @var \Shopsys\ShopBundle\Model\Administrator\AdministratorFacade
     */
    private $administratorFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Grid\GridFactory
     */
    private $gridFactory;

    /**
     * @var \Shopsys\ShopBundle\Model\Administrator\Activity\AdministratorActivityFacade
     */
    private $administratorActivityFacade;

    public function __construct(
        AdministratorFacade $administratorFacade,
        GridFactory $gridFactory,
        Breadcrumb $breadcrumb,
        AdministratorActivityFacade $administratorActivityFacade
    ) {
        $this->administratorFacade = $administratorFacade;
        $this->gridFactory = $gridFactory;
        $this->breadcrumb = $breadcrumb;
        $this->administratorActivityFacade = $administratorActivityFacade;
    }

    /**
     * @Route("/administrator/list/")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function listAction()
    {
        $queryBuilder = $this->administratorFacade->getAllListableQueryBuilder();
        $dataSource = new QueryBuilderDataSource($queryBuilder, 'a.id');

        $grid = $this->gridFactory->create('administratorList', $dataSource);
        $grid->setDefaultOrder('realName');

        $grid->addColumn('realName', 'a.realName', t('Full name'), true);
        $grid->addColumn('email', 'a.email', t('E-mail'));

        $grid->setActionColumnClassAttribute('table-col table-col-10');
        $grid->addEditActionColumn('admin_administrator_edit', ['id' => 'a.id']);
        $grid->addDeleteActionColumn('admin_administrator_delete', ['id' => 'a.id'])
            ->setConfirmMessage(t('Do you really want to remove this administrator?'));

        $grid->setTheme('@ShopsysShop/Admin/Content/Administrator/listGrid.html.twig');

        return $this->render('@ShopsysShop/Admin/Content/Administrator/list.html.twig', [
            'gridView' => $grid->createView(),
        ]);
    }

    /**
     * @Route("/administrator/edit/{id}", requirements={"id" = "\d+"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $id
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function editAction(Request $request, $id)
    {
        $administrator = $this->administratorFacade->getById($id);
        if ($administrator->isSuperadmin()) {
            $message = 'Superadmin cannot be edited.';
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException($message);
        }

        $form = $this->createForm(new AdministratorFormType(AdministratorFormType::SCENARIO_EDIT));

        $administratorData = new AdministratorData();
        $administratorData->setFromEntity($administrator);

        $form->setData($administratorData);
        $form->handleRequest($request);

        if ($form->isValid()) {
            try {
                $this->administratorFacade->edit($id, $administratorData);

                $this->getFlashMessageSender()->addSuccessFlashTwig(
                    t('Administrator <strong><a href="{{ url }}">{{ name }}</a></strong> modified'),
                    [
                        'name' => $administratorData->realName,
                        'url' => $this->generateUrl('admin_administrator_edit', ['id' => $administrator->getId()]),
                    ]
                );
                return $this->redirectToRoute('admin_administrator_list');

            } catch (\Shopsys\ShopBundle\Model\Administrator\Exception\DuplicateSuperadminNameException $ex) {
                $this->getFlashMessageSender()->addErrorFlashTwig(
                    t('We are sorry, but name <strong>{{ name }}</strong> is reserved for system function. Use another one please.'),
                    [
                        'name' => $administratorData->username,
                    ]
                );
            } catch (\Shopsys\ShopBundle\Model\Administrator\Exception\DuplicateUserNameException $ex) {
                $this->getFlashMessageSender()->addErrorFlashTwig(
                    t('Administrator with login name <strong>{{ name }}</strong> already exists'),
                    [
                        'name' => $administratorData->username,
                    ]
                );
            }

        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->getFlashMessageSender()->addErrorFlash(t('Please check the correctness of all data filled.'));
        }

        $this->breadcrumb->overrideLastItem(
            new MenuItem(t('Editing administrator - %name%', ['%name%' => $administrator->getRealName()]))
        );

        $lastAdminActivities = $this->administratorActivityFacade->getLastAdministratorActivities(
            $administrator,
            self::MAX_ADMINISTRATOR_ACTIVITIES_COUNT
        );

        return $this->render('@ShopsysShop/Admin/Content/Administrator/edit.html.twig', [
            'form' => $form->createView(),
            'administrator' => $administrator,
            'lastAdminActivities' => $lastAdminActivities,
        ]);
    }

    /**
     * @Route("/administrator/new/")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function newAction(Request $request)
    {
        $form = $this->createForm(
            new AdministratorFormType(AdministratorFormType::SCENARIO_CREATE),
            null,
            ['validation_groups' => ['Default', AdministratorFormType::SCENARIO_CREATE]]
        );

        $administratorData = new AdministratorData();

        $form->setData($administratorData);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $administratorData = $form->getData();

            try {
                $administrator = $this->administratorFacade->create($administratorData);

                $this->getFlashMessageSender()->addSuccessFlashTwig(
                    t('Administrator <strong><a href="{{ url }}">{{ name }}</a></strong> created'),
                    [
                        'name' => $administrator->getRealName(),
                        'url' => $this->generateUrl('admin_administrator_edit', ['id' => $administrator->getId()]),
                    ]
                );
                return $this->redirectToRoute('admin_administrator_list');

            } catch (\Shopsys\ShopBundle\Model\Administrator\Exception\DuplicateSuperadminNameException $ex) {
                $this->getFlashMessageSender()->addErrorFlashTwig(
                    t('We are sorry, but name <strong>{{ name }}</strong> is reserved for system function. Use another one please.'),
                    [
                        'name' => $administratorData->username,
                    ]
                );
            } catch (\Shopsys\ShopBundle\Model\Administrator\Exception\DuplicateUserNameException $ex) {
                $this->getFlashMessageSender()->addErrorFlashTwig(
                    t('Administrator with login name <strong>{{ name }}</strong> already exists'),
                    [
                        'name' => $administratorData->username,
                    ]
                );
            }

        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->getFlashMessageSender()->addErrorFlash(t('Please check the correctness of all data filled.'));
        }

        return $this->render('@ShopsysShop/Admin/Content/Administrator/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/administrator/delete/{id}", requirements={"id" = "\d+"})
     * @CsrfProtection
     * @param int $id
     */
    public function deleteAction($id)
    {
        try {
            $realName = $this->administratorFacade->getById($id)->getRealName();

            $this->administratorFacade->delete($id);
            $this->getFlashMessageSender()->addSuccessFlashTwig(
                t('Administrator <strong>{{ name }}</strong> deleted.'),
                [
                    'name' => $realName,
                ]
            );
        } catch (\Shopsys\ShopBundle\Model\Administrator\Exception\DeletingSelfException $ex) {
            $this->getFlashMessageSender()->addErrorFlash(t('You can\'t delete yourself.'));
        } catch (\Shopsys\ShopBundle\Model\Administrator\Exception\DeletingLastAdministratorException $ex) {
            $this->getFlashMessageSender()->addErrorFlashTwig(
                t('Administrator <strong>{{ name }}</strong> is the only one and can\'t be deleted.'),
                [
                    'name' => $this->administratorFacade->getById($id)->getRealName(),
                ]
            );
        } catch (\Shopsys\ShopBundle\Model\Administrator\Exception\AdministratorNotFoundException $ex) {
            $this->getFlashMessageSender()->addErrorFlash(t('Selected administrated doesn\'t exist.'));
        }

        return $this->redirectToRoute('admin_administrator_list');
    }
}
