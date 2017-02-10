<?php

namespace Shopsys\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\ShopBundle\Component\Controller\AdminBaseController;
use Shopsys\ShopBundle\Component\Domain\SelectedDomain;
use Shopsys\ShopBundle\Component\Grid\GridFactory;
use Shopsys\ShopBundle\Component\Grid\QueryBuilderWithRowManipulatorDataSource;
use Shopsys\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;
use Shopsys\ShopBundle\Form\Admin\Advert\AdvertFormTypeFactory;
use Shopsys\ShopBundle\Model\Administrator\AdministratorGridFacade;
use Shopsys\ShopBundle\Model\AdminNavigation\Breadcrumb;
use Shopsys\ShopBundle\Model\AdminNavigation\MenuItem;
use Shopsys\ShopBundle\Model\Advert\Advert;
use Shopsys\ShopBundle\Model\Advert\AdvertData;
use Shopsys\ShopBundle\Model\Advert\AdvertFacade;
use Shopsys\ShopBundle\Model\Advert\AdvertPositionList;
use Symfony\Component\HttpFoundation\Request;

class AdvertController extends AdminBaseController
{

    /**
     * @var \Shopsys\ShopBundle\Model\AdminNavigation\Breadcrumb
     */
    private $breadcrumb;

    /**
     * @var \Shopsys\ShopBundle\Model\Administrator\AdministratorGridFacade
     */
    private $administratorGridFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Advert\AdvertFacade
     */
    private $advertFacade;

    /**
     * @var \Shopsys\ShopBundle\Form\Admin\Advert\AdvertFormTypeFactory
     */
    private $advertFormTypeFactory;

    /**
     * @var \Shopsys\ShopBundle\Model\Advert\AdvertPositionList
     */
    private $advertPositionList;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\SelectedDomain
     */
    private $selectedDomain;

    /**
     * @var \Shopsys\ShopBundle\Component\Grid\GridFactory
     */
    private $gridFactory;

    public function __construct(
        AdvertFacade $advertFacade,
        AdministratorGridFacade $administratorGridFacade,
        GridFactory $gridFactory,
        SelectedDomain $selectedDomain,
        Breadcrumb $breadcrumb,
        AdvertFormTypeFactory $advertFormTypeFactory,
        AdvertPositionList $advertPositionList
    ) {
        $this->advertFacade = $advertFacade;
        $this->administratorGridFacade = $administratorGridFacade;
        $this->gridFactory = $gridFactory;
        $this->selectedDomain = $selectedDomain;
        $this->breadcrumb = $breadcrumb;
        $this->advertFormTypeFactory = $advertFormTypeFactory;
        $this->advertPositionList = $advertPositionList;
    }

    /**
     * @Route("/advert/edit/{id}", requirements={"id" = "\d+"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $id
     */
    public function editAction(Request $request, $id) {
        $advert = $this->advertFacade->getById($id);
        $form = $this->createForm($this->advertFormTypeFactory->create($advert));

        $advertData = new AdvertData();
        $advertData->setFromEntity($advert);

        $form->setData($advertData);
        $form->handleRequest($request);

        if ($form->isValid()) {
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

        return $this->render('@ShopsysShop/Admin/Content/Advert/edit.html.twig', [
            'form' => $form->createView(),
            'advert' => $advert,
        ]);
    }

    /**
     * @Route("/advert/list/")
     */
    public function listAction() {
        $administrator = $this->getUser();
        /* @var $administrator \Shopsys\ShopBundle\Model\Administrator\Administrator */

        $queryBuilder = $this->getDoctrine()->getManager()->createQueryBuilder();
        $queryBuilder
            ->select('a')
            ->from(Advert::class, 'a')
            ->where('a.domainId = :selectedDomainId')
            ->setParameter('selectedDomainId', $this->selectedDomain->getId());
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

        $grid->setTheme('@ShopsysShop/Admin/Content/Advert/listGrid.html.twig', [
            'advertPositionsByName' => $this->advertPositionList->getTranslationsIndexedByValue(),
            'TYPE_IMAGE' => Advert::TYPE_IMAGE,
        ]);

        $this->administratorGridFacade->restoreAndRememberGridLimit($administrator, $grid);

        return $this->render('@ShopsysShop/Admin/Content/Advert/list.html.twig', [
            'gridView' => $grid->createView(),
        ]);
    }

    /**
     * @Route("/advert/new/")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function newAction(Request $request) {
        $form = $this->createForm($this->advertFormTypeFactory->create());

        $advertData = new AdvertData();
        $advertData->domainId = $this->selectedDomain->getId();

        $form->setData($advertData);
        $form->handleRequest($request);

        if ($form->isValid()) {
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

        return $this->render('@ShopsysShop/Admin/Content/Advert/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/advert/delete/{id}", requirements={"id" = "\d+"})
     * @CsrfProtection
     * @param int $id
     */
    public function deleteAction($id) {
        try {
            $fullName = $this->advertFacade->getById($id)->getName();

            $this->advertFacade->delete($id);

            $this->getFlashMessageSender()->addSuccessFlashTwig(
                t('Advertising <strong>{{ name }}</strong> deleted'),
                [
                    'name' => $fullName,
                ]
            );
        } catch (\Shopsys\ShopBundle\Model\Advert\Exception\AdvertNotFoundException $ex) {
            $this->getFlashMessageSender()->addErrorFlash(t('Selected advertisement doesn\'t exist.'));
        }

        return $this->redirectToRoute('admin_advert_list');
    }
}
