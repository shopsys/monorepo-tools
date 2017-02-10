<?php

namespace Shopsys\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\ShopBundle\Component\Controller\AdminBaseController;
use Shopsys\ShopBundle\Component\Domain\SelectedDomain;
use Shopsys\ShopBundle\Form\Admin\BestsellingProduct\BestsellingProductFormType;
use Shopsys\ShopBundle\Model\AdminNavigation\Breadcrumb;
use Shopsys\ShopBundle\Model\AdminNavigation\MenuItem;
use Shopsys\ShopBundle\Model\Category\CategoryFacade;
use Shopsys\ShopBundle\Model\Product\BestsellingProduct\ManualBestsellingProductFacade;
use Symfony\Component\HttpFoundation\Request;

class BestsellingProductController extends AdminBaseController
{
    /**
     * @var \Shopsys\ShopBundle\Model\AdminNavigation\Breadcrumb
     */
    private $breadcrumb;

    /**
     * @var \Shopsys\ShopBundle\Model\Category\CategoryFacade
     */
    private $categoryFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\SelectedDomain
     */
    private $selectedDomain;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\BestsellingProduct\ManualBestsellingProductFacade
     */
    private $manualBestsellingProductFacade;

    public function __construct(
        ManualBestsellingProductFacade $manualBestsellingProductFacade,
        CategoryFacade $categoryFacade,
        SelectedDomain $selectedDomain,
        Breadcrumb $breadcrumb
    ) {
        $this->manualBestsellingProductFacade = $manualBestsellingProductFacade;
        $this->categoryFacade = $categoryFacade;
        $this->selectedDomain = $selectedDomain;
        $this->breadcrumb = $breadcrumb;
    }

    /**
     * @Route("/product/bestselling-product/list/")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function listAction(Request $request)
    {
        $domainId = $this->selectedDomain->getId();

        $categoryDetails = $this->categoryFacade->getVisibleCategoryDetailsForDomain($domainId, $request->getLocale());

        $bestsellingProductsInCategories = $this->manualBestsellingProductFacade->getCountsIndexedByCategoryId($domainId);

        return $this->render('@ShopsysShop/Admin/Content/BestsellingProduct/list.html.twig', [
            'categoryDetails' => $categoryDetails,
            'selectedDomainId' => $domainId,
            'bestsellingProductsInCategories' => $bestsellingProductsInCategories,
        ]);
    }

    /**
     * @Route("/product/bestselling-product/detail/")
     */
    public function detailAction(Request $request)
    {
        $form = $this->createForm(new BestsellingProductFormType());

        $category = $this->categoryFacade->getById($request->get('categoryId'));
        $domainId = $request->get('domainId');

        $products = $this->manualBestsellingProductFacade->getProductsIndexedByPosition(
            $category,
            $domainId
        );

        $form->setData(['products' => $products]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $newProducts = $form->getData()['products'];

            $this->manualBestsellingProductFacade->edit($category, $domainId, $newProducts);

            $this->getFlashMessageSender()
                ->addSuccessFlashTwig(
                    t('Best-selling products of category <strong><a href="{{ url }}">{{ name }}</a></strong> set.'),
                    [
                        'name' => $category->getName(),
                        'url' => $this->generateUrl(
                            'admin_bestsellingproduct_detail',
                            ['domainId' => $domainId, 'categoryId' => $category->getId()]
                        ),
                    ]
                );
            return $this->redirectToRoute('admin_bestsellingproduct_list');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->getFlashMessageSender()->addErrorFlashTwig(t('Please check the correctness of all data filled.'));
        }

        $this->breadcrumb->overrideLastItem(new MenuItem($category->getName()));

        return $this->render('@ShopsysShop/Admin/Content/BestsellingProduct/detail.html.twig', [
            'form' => $form->createView(),
            'categoryName' => $category->getName(),
        ]);
    }
}
