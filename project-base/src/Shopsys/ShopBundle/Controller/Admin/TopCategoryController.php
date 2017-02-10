<?php

namespace Shopsys\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\ShopBundle\Component\Controller\AdminBaseController;
use Shopsys\ShopBundle\Component\Domain\SelectedDomain;
use Shopsys\ShopBundle\Form\Admin\Category\TopCategory\TopCategoriesFormTypeFactory;
use Shopsys\ShopBundle\Model\Category\TopCategory\TopCategoryFacade;
use Symfony\Component\HttpFoundation\Request;

class TopCategoryController extends AdminBaseController
{

    /**
     * @var \Shopsys\ShopBundle\Model\Category\TopCategory\TopCategoryFacade
     */
    private $topCategoryFacade;
    /**
     * @var \Shopsys\ShopBundle\Form\Admin\Category\TopCategory\TopCategoriesFormTypeFactory
     */
    private $topCategoriesFormTypeFactory;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\SelectedDomain
     */
    private $selectedDomain;

    public function __construct(
        TopCategoryFacade $topCategoryFacade,
        TopCategoriesFormTypeFactory $topCategoriesFormTypeFactory,
        SelectedDomain $selectedDomain
    ) {
        $this->topCategoryFacade = $topCategoryFacade;
        $this->topCategoriesFormTypeFactory = $topCategoriesFormTypeFactory;
        $this->selectedDomain = $selectedDomain;
    }

    /**
     * @Route("/category/top-category/list/")
     */
    public function listAction(Request $request) {
        $domainId = $this->selectedDomain->getId();

        $form = $this->createForm($this->topCategoriesFormTypeFactory->create($domainId, $request->getLocale()));
        $formData = [
            'categories' => $this->topCategoryFacade->getCategoriesForAll($domainId),
        ];

        $form->setData($formData);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $categories = $form->getData()['categories'];

            $this->topCategoryFacade->saveTopCategoriesForDomain($domainId, $categories);

            $this->getFlashMessageSender()->addSuccessFlash(t('Product settings on the main page successfully changed'));
        }

        return $this->render('@ShopsysShop/Admin/Content/TopCategory/list.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
