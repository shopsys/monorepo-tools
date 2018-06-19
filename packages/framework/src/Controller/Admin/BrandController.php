<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\FrameworkBundle\Component\Controller\AdminBaseController;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Grid\GridFactory;
use Shopsys\FrameworkBundle\Component\Grid\QueryBuilderDataSource;
use Shopsys\FrameworkBundle\Component\Router\Security\Annotation\CsrfProtection;
use Shopsys\FrameworkBundle\Form\Admin\Product\Brand\BrandFormType;
use Shopsys\FrameworkBundle\Model\Administrator\AdministratorGridFacade;
use Shopsys\FrameworkBundle\Model\AdminNavigation\Breadcrumb;
use Shopsys\FrameworkBundle\Model\AdminNavigation\MenuItem;
use Shopsys\FrameworkBundle\Model\Product\Brand\Brand;
use Shopsys\FrameworkBundle\Model\Product\Brand\BrandDataFactory;
use Shopsys\FrameworkBundle\Model\Product\Brand\BrandFacade;
use Symfony\Component\HttpFoundation\Request;

class BrandController extends AdminBaseController
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
     * @var \Shopsys\FrameworkBundle\Model\Product\Brand\BrandFacade
     */
    private $brandFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Grid\GridFactory
     */
    private $gridFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Brand\BrandDataFactory
     */
    private $brandDataFactory;

    public function __construct(
        BrandFacade $brandFacade,
        AdministratorGridFacade $administratorGridFacade,
        GridFactory $gridFactory,
        Breadcrumb $breadcrumb,
        Domain $domain,
        BrandDataFactory $brandDataFactory
    ) {
        $this->brandFacade = $brandFacade;
        $this->administratorGridFacade = $administratorGridFacade;
        $this->gridFactory = $gridFactory;
        $this->breadcrumb = $breadcrumb;
        $this->domain = $domain;
        $this->brandDataFactory = $brandDataFactory;
    }

    /**
     * @Route("/brand/edit/{id}", requirements={"id" = "\d+"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $id
     */
    public function editAction(Request $request, $id)
    {
        $brand = $this->brandFacade->getById($id);
        $brandData = $this->brandDataFactory->createFromBrand($brand);

        $form = $this->createForm(BrandFormType::class, $brandData, ['brand' => $brand]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->brandFacade->edit($id, $brandData);

            $this->getFlashMessageSender()
                ->addSuccessFlashTwig(
                    t('Brand <strong><a href="{{ url }}">{{ name }}</a></strong> modified'),
                    [
                        'name' => $brand->getName(),
                        'url' => $this->generateUrl('admin_brand_edit', ['id' => $brand->getId()]),
                    ]
                );
            return $this->redirectToRoute('admin_brand_list');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->getFlashMessageSender()->addErrorFlashTwig(t('Please check the correctness of all data filled.'));
        }

        $this->breadcrumb->overrideLastItem(new MenuItem(t('Editing brand - %name%', ['%name%' => $brand->getName()])));

        return $this->render('@ShopsysFramework/Admin/Content/Brand/edit.html.twig', [
            'form' => $form->createView(),
            'brand' => $brand,
            'domains' => $this->domain->getAll(),
        ]);
    }

    /**
     * @Route("/brand/list/")
     */
    public function listAction()
    {
        $administrator = $this->getUser();
        /* @var $administrator \Shopsys\FrameworkBundle\Model\Administrator\Administrator */

        $queryBuilder = $this->getDoctrine()->getManager()->createQueryBuilder();
        $queryBuilder->select('b')->from(Brand::class, 'b');
        $dataSource = new QueryBuilderDataSource($queryBuilder, 'b.id');

        $grid = $this->gridFactory->create('brandList', $dataSource);
        $grid->enablePaging();
        $grid->setDefaultOrder('name');

        $grid->addColumn('name', 'b.name', t('Name'), true);

        $grid->setActionColumnClassAttribute('table-col table-col-10');
        $grid->addEditActionColumn('admin_brand_edit', ['id' => 'b.id']);
        $grid->addDeleteActionColumn('admin_brand_delete', ['id' => 'b.id'])
            ->setConfirmMessage(t('Do you really want to remove this brand? If it is used anywhere it will be unset.'));

        $grid->setTheme('@ShopsysFramework/Admin/Content/Brand/listGrid.html.twig');

        $this->administratorGridFacade->restoreAndRememberGridLimit($administrator, $grid);

        return $this->render('@ShopsysFramework/Admin/Content/Brand/list.html.twig', [
            'gridView' => $grid->createView(),
            'domains' => $this->domain->getAll(),
        ]);
    }

    /**
     * @Route("/brand/new/")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function newAction(Request $request)
    {
        $brandData = $this->brandDataFactory->createDefault();

        $form = $this->createForm(BrandFormType::class, $brandData, ['brand' => null]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brand = $this->brandFacade->create($brandData);

            $this->getFlashMessageSender()
                ->addSuccessFlashTwig(
                    t('Brand <strong><a href="{{ url }}">{{ name }}</a></strong> created'),
                    [
                        'name' => $brand->getName(),
                        'url' => $this->generateUrl('admin_brand_edit', ['id' => $brand->getId()]),
                    ]
                );
            return $this->redirectToRoute('admin_brand_list');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->getFlashMessageSender()->addErrorFlashTwig(t('Please check the correctness of all data filled.'));
        }

        return $this->render('@ShopsysFramework/Admin/Content/Brand/new.html.twig', [
            'form' => $form->createView(),
            'domains' => $this->domain->getAll(),
        ]);
    }

    /**
     * @Route("/brand/delete/{id}", requirements={"id" = "\d+"})
     * @CsrfProtection
     * @param int $id
     */
    public function deleteAction($id)
    {
        try {
            $fullName = $this->brandFacade->getById($id)->getName();

            $this->brandFacade->deleteById($id);

            $this->getFlashMessageSender()->addSuccessFlashTwig(
                t('Brand <strong>{{ name }}</strong> deleted'),
                [
                    'name' => $fullName,
                ]
            );
        } catch (\Shopsys\FrameworkBundle\Model\Product\Brand\Exception\BrandNotFoundException $ex) {
            $this->getFlashMessageSender()->addErrorFlash(t('Selected brand doesn\'t exist.'));
        }

        return $this->redirectToRoute('admin_brand_list');
    }
}
