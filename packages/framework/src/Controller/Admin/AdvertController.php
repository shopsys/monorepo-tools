<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\FrameworkBundle\Component\Controller\AdminBaseController;
use Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade;
use Shopsys\FrameworkBundle\Component\Grid\GridFactory;
use Shopsys\FrameworkBundle\Component\Grid\QueryBuilderWithRowManipulatorDataSource;
use Shopsys\FrameworkBundle\Component\Router\Security\Annotation\CsrfProtection;
use Shopsys\FrameworkBundle\Form\Admin\Advert\AdvertFormType;
use Shopsys\FrameworkBundle\Model\Administrator\AdministratorGridFacade;
use Shopsys\FrameworkBundle\Model\AdminNavigation\Breadcrumb;
use Shopsys\FrameworkBundle\Model\AdminNavigation\MenuItem;
use Shopsys\FrameworkBundle\Model\Advert\Advert;
use Shopsys\FrameworkBundle\Model\Advert\AdvertData;
use Shopsys\FrameworkBundle\Model\Advert\AdvertFacade;
use Shopsys\FrameworkBundle\Twig\ImageExtension;
use Symfony\Component\HttpFoundation\Request;

class AdvertController extends AdminBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\AdminNavigation\Breadcrumb
     */
    private $breadcrumb;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Administrator\AdministratorGridFacade
     */
    private $administratorGridFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Advert\AdvertFacade
     */
    private $advertFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade
     */
    private $adminDomainTabsFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Grid\GridFactory
     */
    private $gridFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Twig\ImageExtension
     */
    private $imageExtension;

    public function __construct(
        AdvertFacade $advertFacade,
        AdministratorGridFacade $administratorGridFacade,
        GridFactory $gridFactory,
        AdminDomainTabsFacade $adminDomainTabsFacade,
        Breadcrumb $breadcrumb,
        ImageExtension $imageExtension
    ) {
        $this->advertFacade = $advertFacade;
        $this->administratorGridFacade = $administratorGridFacade;
        $this->gridFactory = $gridFactory;
        $this->adminDomainTabsFacade = $adminDomainTabsFacade;
        $this->breadcrumb = $breadcrumb;
        $this->imageExtension = $imageExtension;
    }

    /**
     * @Route("/advert/edit/{id}", requirements={"id" = "\d+"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $id
     */
    public function editAction(Request $request, $id)
    {
        $advert = $this->advertFacade->getById($id);

        $advertData = new AdvertData();
        $advertData->setFromEntity($advert);

        $form = $this->createForm(AdvertFormType::class, $advertData, [
            'image_exists' => $this->imageExtension->imageExists($advert),
            'scenario' => AdvertFormType::SCENARIO_EDIT,
            'advert' => $advert,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->advertFacade->edit($id, $advertData);

            $this->getFlashMessageSender()
                ->addSuccessFlashTwig(
                    t('Advertising <a href="{{ url }}"><strong>{{ name }}</strong></a> modified'),
                    [
                        'name' => $advert->getName(),
                        'url' => $this->generateUrl('admin_advert_edit', ['id' => $advert->getId()]),
                    ]
                );
            return $this->redirectToRoute('admin_advert_list');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->getFlashMessageSender()->addErrorFlashTwig(t('Please check the correctness of all data filled.'));
        }

        $this->breadcrumb->overrideLastItem(new MenuItem(t('Editing advertising - %name%', ['%name%' => $advert->getName()])));

        return $this->render('@ShopsysFramework/Admin/Content/Advert/edit.html.twig', [
            'form' => $form->createView(),
            'advert' => $advert,
        ]);
    }

    /**
     * @Route("/advert/list/")
     */
    public function listAction()
    {
        $administrator = $this->getUser();
        /* @var $administrator \Shopsys\FrameworkBundle\Model\Administrator\Administrator */

        $queryBuilder = $this->getDoctrine()->getManager()->createQueryBuilder();
        $queryBuilder
            ->select('a')
            ->from(Advert::class, 'a')
            ->where('a.domainId = :selectedDomainId')
            ->setParameter('selectedDomainId', $this->adminDomainTabsFacade->getSelectedDomainId());
        $dataSource = new QueryBuilderWithRowManipulatorDataSource(
            $queryBuilder,
            'a.id',
            function ($row) {
                $advert = $this->advertFacade->getById($row['a']['id']);
                $row['advert'] = $advert;
                return $row;
            }
        );

        $grid = $this->gridFactory->create('advertList', $dataSource);
        $grid->enablePaging();
        $grid->setDefaultOrder('name');

        $grid->addColumn('visible', 'a.hidden', t('Visibility'), true)->setClassAttribute('table-col table-col-10');
        $grid->addColumn('name', 'a.name', t('Name'), true);
        $grid->addColumn('preview', 'a.id', t('Preview'), false);
        $grid->addColumn('positionName', 'a.positionName', t('Area'), true);

        $grid->setActionColumnClassAttribute('table-col table-col-10');
        $grid->addEditActionColumn('admin_advert_edit', ['id' => 'a.id']);
        $grid->addDeleteActionColumn('admin_advert_delete', ['id' => 'a.id'])
            ->setConfirmMessage(t('Do you really want to remove this advert?'));

        $grid->setTheme('@ShopsysFramework/Admin/Content/Advert/listGrid.html.twig', [
            'advertPositionNames' => [
                Advert::POSITION_HEADER => t('under heading'),
                Advert::POSITION_FOOTER => t('above footer'),
                Advert::POSITION_PRODUCT_LIST => t('in category (above the category name)'),
                Advert::POSITION_LEFT_SIDEBAR => t('in left panel (under category tree)'),
            ],
            'TYPE_IMAGE' => Advert::TYPE_IMAGE,
        ]);

        $this->administratorGridFacade->restoreAndRememberGridLimit($administrator, $grid);

        return $this->render('@ShopsysFramework/Admin/Content/Advert/list.html.twig', [
            'gridView' => $grid->createView(),
        ]);
    }

    /**
     * @Route("/advert/new/")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function newAction(Request $request)
    {
        $advertData = new AdvertData();
        $advertData->domainId = $this->adminDomainTabsFacade->getSelectedDomainId();

        $form = $this->createForm(AdvertFormType::class, $advertData, [
            'scenario' => AdvertFormType::SCENARIO_CREATE,
            'advert' => null,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $advertData = $form->getData();

            $advert = $this->advertFacade->create($advertData);

            $this->getFlashMessageSender()
                ->addSuccessFlashTwig(
                    t('Advertising <a href="{{ url }}"><strong>{{ name }}</strong></a> created'),
                    [
                        'name' => $advert->getName(),
                        'url' => $this->generateUrl('admin_advert_edit', ['id' => $advert->getId()]),
                    ]
                );
            return $this->redirectToRoute('admin_advert_list');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->getFlashMessageSender()->addErrorFlashTwig(t('Please check the correctness of all data filled.'));
        }

        return $this->render('@ShopsysFramework/Admin/Content/Advert/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/advert/delete/{id}", requirements={"id" = "\d+"})
     * @CsrfProtection
     * @param int $id
     */
    public function deleteAction($id)
    {
        try {
            $fullName = $this->advertFacade->getById($id)->getName();

            $this->advertFacade->delete($id);

            $this->getFlashMessageSender()->addSuccessFlashTwig(
                t('Advertising <strong>{{ name }}</strong> deleted'),
                [
                    'name' => $fullName,
                ]
            );
        } catch (\Shopsys\FrameworkBundle\Model\Advert\Exception\AdvertNotFoundException $ex) {
            $this->getFlashMessageSender()->addErrorFlash(t('Selected advertisement doesn\'t exist.'));
        }

        return $this->redirectToRoute('admin_advert_list');
    }
}
