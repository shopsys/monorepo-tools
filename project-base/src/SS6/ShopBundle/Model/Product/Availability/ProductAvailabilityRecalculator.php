<?php

namespace SS6\ShopBundle\Model\Product\Availability;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityCalculation;
use SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class ProductAvailabilityRecalculator {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler
	 */
	private $productAvailabilityRecalculationScheduler;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityCalculation
	 */
	private $productAvailabilityCalculation;

	public function __construct(
		EntityManager $em,
		ProductAvailabilityRecalculationScheduler $productAvailabilityRecalculationScheduler,
		ProductAvailabilityCalculation $productAvailabilityCalculation
	) {
		$this->em = $em;
		$this->productAvailabilityRecalculationScheduler = $productAvailabilityRecalculationScheduler;
		$this->productAvailabilityCalculation = $productAvailabilityCalculation;
	}

	public function runScheduledRecalculations() {
		$products = $this->productAvailabilityRecalculationScheduler->getProductsScheduledForRecalculation();
		$this->recalculateAvailabilityForProducts($products);
		$this->productAvailabilityRecalculationScheduler->cleanSchedule();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product[] $products
	 */
	private function recalculateAvailabilityForProducts(array $products) {
		foreach ($products as $product) {
			$calculatedAvailability = $this->productAvailabilityCalculation->getCalculatedAvailability($product);
			$product->setCalculatedAvailability($calculatedAvailability);
		}
		$this->em->flush($products);
	}

	/**
	 * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
	 */
	public function onKernelResponse(FilterResponseEvent $event) {
		if (!$event->isMasterRequest()) {
			return;
		}

		$this->runScheduledRecalculations();
	}

}
