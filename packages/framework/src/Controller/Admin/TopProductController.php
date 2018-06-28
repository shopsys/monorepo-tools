<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade;
use Shopsys\FrameworkBundle\Form\Admin\Product\TopProduct\TopProductsFormType;
use Shopsys\FrameworkBundle\Model\Product\TopProduct\TopProductFacade;
use Symfony\Component\HttpFoundation\Request;

class TopProductController extends AdminBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\TopProduct\TopProductFacade
     */
    private $topProductFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade
     */
    private $adminDomainTabsFacade;

    public function __construct(
        TopProductFacade $topProductFacade,
        AdminDomainTabsFacade $adminDomainTabsFacade
    ) {
        $this->topProductFacade = $topProductFacade;
        $this->adminDomainTabsFacade = $adminDomainTabsFacade;
    }

    /**
     * @Route("/product/top-product/list/")
     */
    public function listAction(Request $request)
    {
        $domainId = $this->adminDomainTabsFacade->getSelectedDomainId();
        $formData = [
            'products' => $this->getProductsForDomain($domainId),
        ];

        $form = $this->createForm(TopProductsFormType::class, $formData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $products = $form->getData()['products'];

            $this->topProductFacade->saveTopProductsForDomain($domainId, $products);

            $this->getFlashMessageSender()->addSuccessFlash(t('Product settings on the main page successfully changed'));
        }

        return $this->render('@ShopsysFramework/Admin/Content/TopProduct/list.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Product\Product[]
     */
    private function getProductsForDomain($domainId)
    {
        $topProducts = $this->topProductFacade->getAll($domainId);
        $products = [];

        foreach ($topProducts as $topProduct) {
            $products[] = $topProduct->getProduct();
        }

        return $products;
    }
}
