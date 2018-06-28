<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\FrameworkBundle\Component\Grid\GridFactory;
use Shopsys\FrameworkBundle\Component\Grid\QueryBuilderWithRowManipulatorDataSource;
use Shopsys\FrameworkBundle\Form\Admin\QuickSearch\QuickSearchFormData;
use Shopsys\FrameworkBundle\Form\Admin\QuickSearch\QuickSearchFormType;
use Shopsys\FrameworkBundle\Model\Administrator\AdministratorGridFacade;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\AdvancedSearchFacade;
use Shopsys\FrameworkBundle\Model\Product\Listing\ProductListAdminFacade;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductFacade;
use Symfony\Component\HttpFoundation\Request;

class ProductPickerController extends AdminBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Administrator\AdministratorGridFacade
     */
    private $administratorGridFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\AdvancedSearch\AdvancedSearchFacade
     */
    private $advancedSearchFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Grid\GridFactory
     */
    private $gridFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Listing\ProductListAdminFacade
     */
    private $productListAdminFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductFacade
     */
    private $productFacade;

    public function __construct(
        AdministratorGridFacade $administratorGridFacade,
        GridFactory $gridFactory,
        ProductListAdminFacade $productListAdminFacade,
        AdvancedSearchFacade $advancedSearchFacade,
        ProductFacade $productFacade
    ) {
        $this->administratorGridFacade = $administratorGridFacade;
        $this->gridFactory = $gridFactory;
        $this->productListAdminFacade = $productListAdminFacade;
        $this->advancedSearchFacade = $advancedSearchFacade;
        $this->productFacade = $productFacade;
    }

    /**
     * @Route("/product-picker/pick-multiple/{jsInstanceId}/")
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $jsInstanceId
     */
    public function pickMultipleAction(Request $request, $jsInstanceId)
    {
        return $this->getPickerResponse(
            $request,
            [
                'isMultiple' => true,
            ],
            [
                'isMultiple' => true,
                'jsInstanceId' => $jsInstanceId,
                'allowMainVariants' => $request->query->getBoolean('allowMainVariants', true),
                'allowVariants' => $request->query->getBoolean('allowVariants', true),
            ]
        );
    }

    /**
     * @Route("/product-picker/pick-single/{parentInstanceId}/", defaults={"parentInstanceId"="__instance_id__"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $parentInstanceId
     */
    public function pickSingleAction(Request $request, $parentInstanceId)
    {
        return $this->getPickerResponse(
            $request,
            [
                'isMultiple' => false,
            ],
            [
                'isMultiple' => false,
                'parentInstanceId' => $parentInstanceId,
                'allowMainVariants' => $request->query->getBoolean('allowMainVariants', true),
                'allowVariants' => $request->query->getBoolean('allowVariants', true),
            ]
        );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param array $viewParameters
     * @param array $gridViewParameters
     */
    private function getPickerResponse(Request $request, array $viewParameters, array $gridViewParameters)
    {
        $administrator = $this->getUser();
        /* @var $administrator \Shopsys\FrameworkBundle\Model\Administrator\Administrator */

        $advancedSearchForm = $this->advancedSearchFacade->createAdvancedSearchForm($request);
        $advancedSearchData = $advancedSearchForm->getData();
        $quickSearchData = new QuickSearchFormData();

        $quickSearchForm = $this->createForm(QuickSearchFormType::class, $quickSearchData);
        $quickSearchForm->handleRequest($request);

        $isAdvancedSearchFormSubmitted = $this->advancedSearchFacade->isAdvancedSearchFormSubmitted($request);
        if ($isAdvancedSearchFormSubmitted) {
            $queryBuilder = $this->advancedSearchFacade->getQueryBuilderByAdvancedSearchData($advancedSearchData);
        } else {
            $queryBuilder = $this->productListAdminFacade->getQueryBuilderByQuickSearchData($quickSearchData);
        }

        $dataSource = new QueryBuilderWithRowManipulatorDataSource(
            $queryBuilder,
            'p.id',
            function ($row) {
                $product = $this->productFacade->getById($row['p']['id']);
                $row['product'] = $product;
                return $row;
            }
        );

        $grid = $this->gridFactory->create('productPicker', $dataSource);
        $grid->enablePaging();
        $grid->setDefaultOrder('name');

        $grid->addColumn('name', 'pt.name', t('Name'), true);
        $grid->addColumn('catnum', 'p.catnum', t('Catalogue number'), true);
        $grid->addColumn('calculatedVisibility', 'p.calculatedVisibility', t('Visibility'), true)
            ->setClassAttribute('table-col table-col-10 text-center');
        $grid->addColumn('select', 'p.id', '')->setClassAttribute('table-col table-col-15 text-center');

        $gridViewParameters['VARIANT_TYPE_MAIN'] = Product::VARIANT_TYPE_MAIN;
        $gridViewParameters['VARIANT_TYPE_VARIANT'] = Product::VARIANT_TYPE_VARIANT;
        $grid->setTheme('@ShopsysFramework/Admin/Content/ProductPicker/listGrid.html.twig', $gridViewParameters);

        $this->administratorGridFacade->restoreAndRememberGridLimit($administrator, $grid);

        $viewParameters['gridView'] = $grid->createView();
        $viewParameters['quickSearchForm'] = $quickSearchForm->createView();
        $viewParameters['advancedSearchForm'] = $advancedSearchForm->createView();
        $viewParameters['isAdvancedSearchFormSubmitted'] = $this->advancedSearchFacade->isAdvancedSearchFormSubmitted($request);

        return $this->render('@ShopsysFramework/Admin/Content/ProductPicker/list.html.twig', $viewParameters);
    }
}
