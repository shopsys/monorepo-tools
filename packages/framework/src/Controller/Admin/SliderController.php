<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade;
use Shopsys\FrameworkBundle\Component\Grid\GridFactory;
use Shopsys\FrameworkBundle\Component\Grid\QueryBuilderDataSource;
use Shopsys\FrameworkBundle\Component\Router\Security\Annotation\CsrfProtection;
use Shopsys\FrameworkBundle\Form\Admin\Slider\SliderItemFormType;
use Shopsys\FrameworkBundle\Model\AdminNavigation\Breadcrumb;
use Shopsys\FrameworkBundle\Model\AdminNavigation\MenuItem;
use Shopsys\FrameworkBundle\Model\Slider\SliderItem;
use Shopsys\FrameworkBundle\Model\Slider\SliderItemData;
use Shopsys\FrameworkBundle\Model\Slider\SliderItemFacade;
use Symfony\Component\HttpFoundation\Request;

class SliderController extends AdminBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\AdminNavigation\Breadcrumb
     */
    private $breadcrumb;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade
     */
    private $adminDomainTabsFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Grid\GridFactory
     */
    private $gridFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Slider\SliderItemFacade
     */
    private $sliderItemFacade;

    public function __construct(
        SliderItemFacade $sliderItemFacade,
        GridFactory $gridFactory,
        AdminDomainTabsFacade $adminDomainTabsFacade,
        Breadcrumb $breadcrumb
    ) {
        $this->sliderItemFacade = $sliderItemFacade;
        $this->gridFactory = $gridFactory;
        $this->adminDomainTabsFacade = $adminDomainTabsFacade;
        $this->breadcrumb = $breadcrumb;
    }

    /**
     * @Route("/slider/list/")
     */
    public function listAction()
    {
        $queryBuilder = $this->getDoctrine()->getManager()->createQueryBuilder();
        $queryBuilder
            ->select('s')
            ->from(SliderItem::class, 's')
            ->where('s.domainId = :selectedDomainId')
            ->setParameter('selectedDomainId', $this->adminDomainTabsFacade->getSelectedDomainId());
        $dataSource = new QueryBuilderDataSource($queryBuilder, 's.id');

        $grid = $this->gridFactory->create('sliderItemList', $dataSource);
        $grid->enableDragAndDrop(SliderItem::class);

        $grid->addColumn('name', 's.name', t('Name'));
        $grid->addColumn('link', 's.link', t('Link'));
        $grid->addEditActionColumn('admin_slider_edit', ['id' => 's.id']);
        $grid->addDeleteActionColumn('admin_slider_delete', ['id' => 's.id'])
            ->setConfirmMessage(t('Do you really want to remove this page?'));

        $grid->setTheme('@ShopsysFramework/Admin/Content/Slider/listGrid.html.twig');

        return $this->render('@ShopsysFramework/Admin/Content/Slider/list.html.twig', [
            'gridView' => $grid->createView(),
        ]);
    }

    /**
     * @Route("/slider/item/new/")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function newAction(Request $request)
    {
        $sliderItemData = new SliderItemData();
        $sliderItemData->domainId = $this->adminDomainTabsFacade->getSelectedDomainId();

        $form = $this->createForm(SliderItemFormType::class, $sliderItemData, [
            'scenario' => SliderItemFormType::SCENARIO_CREATE,
            'slider_item' => null,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sliderItem = $this->sliderItemFacade->create($form->getData());

            $this->getFlashMessageSender()->addSuccessFlashTwig(
                t('Slider page <strong><a href="{{ url }}">{{ name }}</a></strong> created'),
                [
                    'name' => $sliderItem->getName(),
                    'url' => $this->generateUrl('admin_slider_edit', ['id' => $sliderItem->getId()]),
                ]
            );
            return $this->redirectToRoute('admin_slider_list');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->getFlashMessageSender()->addErrorFlashTwig(t('Please check the correctness of all data filled.'));
        }

        return $this->render('@ShopsysFramework/Admin/Content/Slider/new.html.twig', [
            'form' => $form->createView(),
            'selectedDomainId' => $this->adminDomainTabsFacade->getSelectedDomainId(),
        ]);
    }

    /**
     * @Route("/slider/item/edit/{id}", requirements={"id"="\d+"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $id
     */
    public function editAction(Request $request, $id)
    {
        $sliderItem = $this->sliderItemFacade->getById($id);
        $sliderItemData = new SliderItemData();
        $sliderItemData->setFromEntity($sliderItem);

        $form = $this->createForm(SliderItemFormType::class, $sliderItemData, [
            'scenario' => SliderItemFormType::SCENARIO_EDIT,
            'slider_item' => $sliderItem,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->sliderItemFacade->edit($id, $sliderItemData);

            $this->getFlashMessageSender()->addSuccessFlashTwig(
                t('Slider page <strong><a href="{{ url }}">{{ name }}</a></strong> modified'),
                [
                    'name' => $sliderItem->getName(),
                    'url' => $this->generateUrl('admin_slider_edit', ['id' => $sliderItem->getId()]),
                ]
            );

            return $this->redirectToRoute('admin_slider_list');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->getFlashMessageSender()->addErrorFlash(t('Please check the correctness of all data filled.'));
        }

        $this->breadcrumb->overrideLastItem(
            new MenuItem(t('Editing slider page - %name%', ['%name%' => $sliderItem->getName()]))
        );

        return $this->render('@ShopsysFramework/Admin/Content/Slider/edit.html.twig', [
            'form' => $form->createView(),
            'sliderItem' => $sliderItem,
        ]);
    }

    /**
     * @Route("/slider/item/delete/{id}", requirements={"id" = "\d+"})
     * @CsrfProtection
     * @param int $id
     */
    public function deleteAction($id)
    {
        try {
            $name = $this->sliderItemFacade->getById($id)->getName();

            $this->sliderItemFacade->delete($id);

            $this->getFlashMessageSender()->addSuccessFlashTwig(
                t('Page <strong>{{ name }}</strong> deleted'),
                [
                    'name' => $name,
                ]
            );
        } catch (\Shopsys\FrameworkBundle\Model\Slider\Exception\SliderItemNotFoundException $ex) {
            $this->getFlashMessageSender()->addErrorFlash(t('Selected page doesn\'t exist.'));
        }

        return $this->redirectToRoute('admin_slider_list');
    }
}
