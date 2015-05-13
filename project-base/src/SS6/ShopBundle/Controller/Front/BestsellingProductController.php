<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Model\Category\Category;
use SS6\ShopBundle\Model\Customer\CurrentCustomer;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Product\BestsellingProduct\BestsellingProductFacade;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BestsellingProductController extends Controller {

	/**
	 * @var \SS6\ShopBundle\Model\Product\BestsellingProduct\BestsellingProductFacade
	 */
	private $bestsellingProductsFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\CurrentCustomer
	 */
	private $currentCustomer;

	public function __construct(
		BestsellingProductFacade $bestsellingProductFacade,
		Domain $domain,
		CurrentCustomer $currentCustomer
	) {
		$this->bestsellingProductsFacade = $bestsellingProductFacade;
		$this->domain = $domain;
		$this->currentCustomer = $currentCustomer;
	}

	public function listAction(Category $category) {
		$bestsellingProducts = $this->bestsellingProductsFacade->getAllListableProductDetails(
			$this->domain->getId(), $category, $this->currentCustomer->getPricingGroup()
		);

		return $this->render('@SS6Shop/Front/Content/Product/bestsellingProductsList.html.twig', [
			'productDetails' => $bestsellingProducts,
			'maxShownProducts' => BestsellingProductFacade::MAX_SHOW_RESULTS,
		]);
	}

}
