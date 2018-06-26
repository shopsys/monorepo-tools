<?php

namespace Shopsys\ShopBundle\Controller\Front;

use Shopsys\FrameworkBundle\Component\Controller\FrontBaseController;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Category\Category;
use Shopsys\FrameworkBundle\Model\Customer\CurrentCustomer;
use Shopsys\FrameworkBundle\Model\Product\BestsellingProduct\BestsellingProductFacade;
use Shopsys\FrameworkBundle\Model\Product\BestsellingProduct\CachedBestsellingProductFacade;

class BestsellingProductController extends FrontBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\BestsellingProduct\CachedBestsellingProductFacade
     */
    private $cachedBestsellingProductFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\CurrentCustomer
     */
    private $currentCustomer;

    public function __construct(
        CachedBestsellingProductFacade $cachedBestsellingProductFacade,
        Domain $domain,
        CurrentCustomer $currentCustomer
    ) {
        $this->cachedBestsellingProductFacade = $cachedBestsellingProductFacade;
        $this->domain = $domain;
        $this->currentCustomer = $currentCustomer;
    }

    public function listAction(Category $category)
    {
        $bestsellingProducts = $this->cachedBestsellingProductFacade->getAllOfferedBestsellingProducts(
            $this->domain->getId(),
            $category,
            $this->currentCustomer->getPricingGroup()
        );

        return $this->render('@ShopsysShop/Front/Content/Product/bestsellingProductsList.html.twig', [
            'products' => $bestsellingProducts,
            'maxShownProducts' => BestsellingProductFacade::MAX_SHOW_RESULTS,
        ]);
    }
}
