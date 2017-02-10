<?php

namespace Shopsys\ShopBundle\Controller\Front;

use Shopsys\ShopBundle\Component\Controller\FrontBaseController;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Model\Category\Category;
use Shopsys\ShopBundle\Model\Customer\CurrentCustomer;
use Shopsys\ShopBundle\Model\Product\BestsellingProduct\BestsellingProductFacade;
use Shopsys\ShopBundle\Model\Product\BestsellingProduct\CachedBestsellingProductFacade;

class BestsellingProductController extends FrontBaseController {

    /**
     * @var \Shopsys\ShopBundle\Model\Product\BestsellingProduct\CachedBestsellingProductFacade
     */
    private $cachedBestsellingProductFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\ShopBundle\Model\Customer\CurrentCustomer
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

    public function listAction(Category $category) {
        $bestsellingProducts = $this->cachedBestsellingProductFacade->getAllOfferedProductDetails(
            $this->domain->getId(), $category, $this->currentCustomer->getPricingGroup()
        );

        return $this->render('@ShopsysShop/Front/Content/Product/bestsellingProductsList.html.twig', [
            'productDetails' => $bestsellingProducts,
            'maxShownProducts' => BestsellingProductFacade::MAX_SHOW_RESULTS,
        ]);
    }

}
