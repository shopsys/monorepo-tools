<?php

namespace Shopsys\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\ShopBundle\Component\Controller\AdminBaseController;
use Shopsys\ShopBundle\Component\Domain\SelectedDomain;
use Shopsys\ShopBundle\Form\Admin\Product\TopProduct\TopProductsFormTypeFactory;
use Shopsys\ShopBundle\Model\Product\TopProduct\TopProductFacade;
use Symfony\Component\HttpFoundation\Request;

class TopProductController extends AdminBaseController
{
    /**
     * @var \Shopsys\ShopBundle\Model\Product\TopProduct\TopProductFacade
     */
    private $topProductFacade;
    /**
     * @var \Shopsys\ShopBundle\Form\Admin\Product\TopProduct\TopProductsFormTypeFactory
     */
    private $topProductsFormTypeFactory;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\SelectedDomain
     */
    private $selectedDomain;

    public function __construct(
        TopProductFacade $topProductFacade,
        TopProductsFormTypeFactory $topProductsFormTypeFactory,
        SelectedDomain $selectedDomain
    ) {
        $this->topProductFacade = $topProductFacade;
        $this->topProductsFormTypeFactory = $topProductsFormTypeFactory;
        $this->selectedDomain = $selectedDomain;
    }

    /**
     * @Route("/product/top-product/list/")
     */
    public function listAction(Request $request) {
        $form = $this->createForm($this->topProductsFormTypeFactory->create());

        $domainId = $this->selectedDomain->getId();
        $formData = [
            'products' => $this->getProductsForDomain($domainId),
        ];

        $form->setData($formData);
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
    private function getProductsForDomain($domainId) {
        $topProducts = $this->topProductFacade->getAll($domainId);
        $products = [];

        foreach ($topProducts as $topProduct) {
            $products[] = $topProduct->getProduct();
        }

        return $products;
    }
}
