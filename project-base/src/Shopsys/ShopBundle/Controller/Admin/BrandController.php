<?php

namespace Shopsys\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\ShopBundle\Component\Controller\AdminBaseController;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Grid\GridFactory;
use Shopsys\ShopBundle\Component\Grid\QueryBuilderDataSource;
use Shopsys\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;
use Shopsys\ShopBundle\Form\Admin\Product\Brand\BrandFormType;
use Shopsys\ShopBundle\Model\Administrator\AdministratorGridFacade;
use Shopsys\ShopBundle\Model\AdminNavigation\Breadcrumb;
use Shopsys\ShopBundle\Model\AdminNavigation\MenuItem;
use Shopsys\ShopBundle\Model\Product\Brand\Brand;
use Shopsys\ShopBundle\Model\Product\Brand\BrandData;
use Shopsys\ShopBundle\Model\Product\Brand\BrandDataFactory;
use Shopsys\ShopBundle\Model\Product\Brand\BrandFacade;
use Symfony\Component\HttpFoundation\Request;

class BrandController extends AdminBaseController
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
     * @var \Shopsys\ShopBundle\Model\Product\Brand\BrandFacade
     */
    private $brandFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Grid\GridFactory
     */
    private $gridFactory;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Brand\BrandDataFactory
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
        $form = $this->createForm(new BrandFormType($brand));

        $brandData = $this->brandDataFactory->createFromBrand($brand);

        $form->setData($brandData);
        $form->handleRequest($request);

        if ($form->isValid()) {
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

        return $this->render('@ShopsysShop/Admin/Content/Brand/edit.html.twig', [
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
        /* @var $administrator \Shopsys\ShopBundle\Model\Administrator\Administrator */

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

        $grid->setTheme('@ShopsysShop/Admin/Content/Brand/listGrid.html.twig');

        $this->administratorGridFacade->restoreAndRememberGridLimit($administrator, $grid);

        return $this->render('@ShopsysShop/Admin/Content/Brand/list.html.twig', [
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
        $form = $this->createForm(new BrandFormType());

        $brandData = new BrandData();

        $form->setData($brandData);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $brandData = $form->getData();

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

        return $this->render('@ShopsysShop/Admin/Content/Brand/new.html.twig', [
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
        } catch (\Shopsys\ShopBundle\Model\Product\Brand\Exception\BrandNotFoundException $ex) {
            $this->getFlashMessageSender()->addErrorFlash(t('Selected brand doesn\'t exist.'));
        }

        return $this->redirectToRoute('admin_brand_list');
    }
}
