<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade;
use Shopsys\FrameworkBundle\Form\Admin\Category\TopCategory\TopCategoriesFormType;
use Shopsys\FrameworkBundle\Model\Category\TopCategory\TopCategoryFacade;
use Symfony\Component\HttpFoundation\Request;

class TopCategoryController extends AdminBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\TopCategory\TopCategoryFacade
     */
    private $topCategoryFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade
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
        $domainId = $this->adminDomainTabsFacade->getSelectedDomainId();
        $formData = [
            'categories' => $this->topCategoryFacade->getAllCategoriesByDomainId($domainId),
        ];

        $form = $this->createForm(TopCategoriesFormType::class, $formData, [
            'domain_id' => $domainId,
            'locale' => $request->getLocale(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categories = $form->getData()['categories'];

            $this->topCategoryFacade->saveTopCategoriesForDomain($domainId, $categories);

            $this->getFlashMessageSender()->addSuccessFlash(t('Product settings on the main page successfully changed'));
        }

        return $this->render('@ShopsysFramework/Admin/Content/TopCategory/list.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
