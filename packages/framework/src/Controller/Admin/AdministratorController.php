<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\FrameworkBundle\Component\Grid\GridFactory;
use Shopsys\FrameworkBundle\Component\Grid\QueryBuilderDataSource;
use Shopsys\FrameworkBundle\Component\Router\Security\Annotation\CsrfProtection;
use Shopsys\FrameworkBundle\Form\Admin\Administrator\AdministratorFormType;
use Shopsys\FrameworkBundle\Model\Administrator\Activity\AdministratorActivityFacade;
use Shopsys\FrameworkBundle\Model\Administrator\Administrator;
use Shopsys\FrameworkBundle\Model\Administrator\AdministratorData;
use Shopsys\FrameworkBundle\Model\Administrator\AdministratorFacade;
use Shopsys\FrameworkBundle\Model\AdminNavigation\Breadcrumb;
use Shopsys\FrameworkBundle\Model\AdminNavigation\MenuItem;
use Symfony\Component\HttpFoundation\Request;

class AdministratorController extends AdminBaseController
{
    const MAX_ADMINISTRATOR_ACTIVITIES_COUNT = 10;

    /**
     * @var \Shopsys\FrameworkBundle\Model\AdminNavigation\Breadcrumb
     */
    private $breadcrumb;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Administrator\AdministratorFacade
     */
    private $administratorFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Grid\GridFactory
     */
    private $gridFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Administrator\Activity\AdministratorActivityFacade
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

        $grid->setTheme('@ShopsysFramework/Admin/Content/Administrator/listGrid.html.twig');

        return $this->render('@ShopsysFramework/Admin/Content/Administrator/list.html.twig', [
            'gridView' => $grid->createView(),
        ]);
    }

    /**
     * @Route("/administrator/edit/{id}", requirements={"id" = "\d+"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $id
     */
    public function editAction(Request $request, $id)
    {
        $administrator = $this->administratorFacade->getById($id);

        $loggedUser = $this->getUser();
        if (!$loggedUser instanceof Administrator) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException(sprintf(
                'Logged user is not instance of "%s". That should not happen due to security.yml configuration.',
                Administrator::class
            ));
        }

        if ($administrator->isSuperadmin() && !$loggedUser->isSuperadmin()) {
            $message = 'Superadmin can only be edited by superadmin.';
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException($message);
        }

        $administratorData = new AdministratorData();
        $administratorData->setFromEntity($administrator);

        $form = $this->createForm(AdministratorFormType::class, $administratorData, [
            'administrator' => $administrator,
            'scenario' => AdministratorFormType::SCENARIO_EDIT,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
            } catch (\Shopsys\FrameworkBundle\Model\Administrator\Exception\DuplicateUserNameException $ex) {
                $this->getFlashMessageSender()->addErrorFlashTwig(
                    t('Login name <strong>{{ name }}</strong> is already used'),
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

        return $this->render('@ShopsysFramework/Admin/Content/Administrator/edit.html.twig', [
            'form' => $form->createView(),
            'administrator' => $administrator,
            'lastAdminActivities' => $lastAdminActivities,
        ]);
    }

    /**
     * @Route("/administrator/my-account/")
     */
    public function myAccountAction()
    {
        $loggedUser = $this->getUser();
        /* @var $loggedUser \Shopsys\FrameworkBundle\Model\Administrator\Administrator */

        return $this->redirectToRoute('admin_administrator_edit', [
            'id' => $loggedUser->getId(),
        ]);
    }

    /**
     * @Route("/administrator/new/")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function newAction(Request $request)
    {
        $form = $this->createForm(AdministratorFormType::class, new AdministratorData(), [
            'scenario' => AdministratorFormType::SCENARIO_CREATE,
            'administrator' => null,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
            } catch (\Shopsys\FrameworkBundle\Model\Administrator\Exception\DuplicateUserNameException $ex) {
                $this->getFlashMessageSender()->addErrorFlashTwig(
                    t('Login name <strong>{{ name }}</strong> is already used'),
                    [
                        'name' => $administratorData->username,
                    ]
                );
            }
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->getFlashMessageSender()->addErrorFlash(t('Please check the correctness of all data filled.'));
        }

        return $this->render('@ShopsysFramework/Admin/Content/Administrator/new.html.twig', [
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
        } catch (\Shopsys\FrameworkBundle\Model\Administrator\Exception\DeletingSelfException $ex) {
            $this->getFlashMessageSender()->addErrorFlash(t('You can\'t delete yourself.'));
        } catch (\Shopsys\FrameworkBundle\Model\Administrator\Exception\DeletingLastAdministratorException $ex) {
            $this->getFlashMessageSender()->addErrorFlashTwig(
                t('Administrator <strong>{{ name }}</strong> is the only one and can\'t be deleted.'),
                [
                    'name' => $this->administratorFacade->getById($id)->getRealName(),
                ]
            );
        } catch (\Shopsys\FrameworkBundle\Model\Administrator\Exception\AdministratorNotFoundException $ex) {
            $this->getFlashMessageSender()->addErrorFlash(t('Selected administrated doesn\'t exist.'));
        }

        return $this->redirectToRoute('admin_administrator_list');
    }
}
