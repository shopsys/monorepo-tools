<?php

namespace Shopsys\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\ShopBundle\Component\Controller\AdminBaseController;
use Shopsys\ShopBundle\Component\Domain\AdminDomainTabsFacade;
use Shopsys\ShopBundle\Form\Admin\Product\TopProduct\TopProductsFormType;
use Shopsys\ShopBundle\Model\Product\TopProduct\TopProductFacade;
use Symfony\Component\HttpFoundation\Request;

class TopProductController extends AdminBaseController
{
    /**
     * @var \Shopsys\ShopBundle\Model\Product\TopProduct\TopProductFacade
     */
    private $topProductFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\AdminDomainTabsFacade
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

        if ($form->isValid()) {
            $products = $form->getData()['products'];

            $this->topProductFacade->saveTopProductsForDomain($domainId, $products);

            $this->getFlashMessageSender()->addSuccessFlash(t('Product settings on the main page successfully changed'));
        }

        return $this->render('@ShopsysShop/Admin/Content/TopProduct/list.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Product\Product[]
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
