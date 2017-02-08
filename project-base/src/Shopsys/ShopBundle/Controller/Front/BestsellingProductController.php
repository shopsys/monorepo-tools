<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Component\Controller\FrontBaseController;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Model\Category\Category;
use SS6\ShopBundle\Model\Customer\CurrentCustomer;
use SS6\ShopBundle\Model\Product\BestsellingProduct\BestsellingProductFacade;
use SS6\ShopBundle\Model\Product\BestsellingProduct\CachedBestsellingProductFacade;

class BestsellingProductController extends FrontBaseController {

	/**
	 * @var \SS6\ShopBundle\Model\Product\BestsellingProduct\CachedBestsellingProductFacade
	 */
	private $cachedBestsellingProductFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\CurrentCustomer
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

		return $this->render('@SS6Shop/Front/Content/Product/bestsellingProductsList.html.twig', [
			'productDetails' => $bestsellingProducts,
			'maxShownProducts' => BestsellingProductFacade::MAX_SHOW_RESULTS,
		]);
	}

}
