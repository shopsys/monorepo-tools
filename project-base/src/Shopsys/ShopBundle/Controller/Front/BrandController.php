<?php

namespace Shopsys\ShopBundle\Controller\Front;

use Shopsys\FrameworkBundle\Model\Product\Brand\BrandFacade;

class BrandController extends FrontBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Brand\BrandFacade
     */
    private $brandFacade;

    public function __construct(
        BrandFacade $brandFacade
    ) {
        $this->brandFacade = $brandFacade;
    }

    public function listAction()
    {
        return $this->render('@ShopsysShop/Front/Content/Brand/list.html.twig', [
            'brands' => $this->brandFacade->getAll(),
        ]);
    }
}
