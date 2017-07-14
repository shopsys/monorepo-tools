<?php

namespace Shopsys\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\ShopBundle\Component\Controller\AdminBaseController;
use Shopsys\ShopBundle\Component\Domain\AdminDomainTabsFacade;
use Shopsys\ShopBundle\Form\Admin\Category\TopCategory\TopCategoriesFormType;
use Shopsys\ShopBundle\Model\Category\TopCategory\TopCategoryFacade;
use Symfony\Component\HttpFoundation\Request;

class TopCategoryController extends AdminBaseController
{
    /**
     * @var \Shopsys\ShopBundle\Model\Category\TopCategory\TopCategoryFacade
     */
    private $topCategoryFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\AdminDomainTabsFacade
     */
    private $adminDomainTabsFacade;

    public function __construct(
        TopCategoryFacade $topCategoryFacade,
        AdminDomainTabsFacade $adminDomainTabsFacade
    ) {
        $this->topCategoryFacade = $topCategoryFacade;
        $this->adminDomainTabsFacade = $adminDomainTabsFacade;
    }

    /**
     * @Route("/category/top-category/list/")
     */
    public function listAction(Request $request)
    {
        $domainId = $this->adminDomainTabsFacade->getId();
        $formData = [
            'categories' => $this->topCategoryFacade->getAllCategoriesByDomainId($domainId),
        ];

        $form = $this->createForm(TopCategoriesFormType::class, $formData, [
            'domain_id' => $domainId,
            'locale' => $request->getLocale(),
        ]);
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
