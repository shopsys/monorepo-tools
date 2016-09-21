<?php

namespace SS6\ShopBundle\Model\Product\Availability;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\Doctrine\EntityManagerFacade;
use SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityCalculation;
use SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler;
use SS6\ShopBundle\Model\Product\Product;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class ProductAvailabilityRecalculator {

	const BATCH_SIZE = 100;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Component\Doctrine\EntityManagerFacade
	 */
	private $entityManagerFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler
	 */
	private $productAvailabilityRecalculationScheduler;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityCalculation
	 */
	private $productAvailabilityCalculation;

	/**
	 * @var \Doctrine\ORM\Internal\Hydration\IterableResult|\SS6\ShopBundle\Model\Product\Product[][0]|null
	 */
	private $productRowsIterator;

	public function __construct(
		EntityManager $em,
		EntityManagerFacade $entityManagerFacade,
		ProductAvailabilityRecalculationScheduler $productAvailabilityRecalculationScheduler,
		ProductAvailabilityCalculation $productAvailabilityCalculation
	) {
		$this->em = $em;
		$this->entityManagerFacade = $entityManagerFacade;
		$this->productAvailabilityRecalculationScheduler = $productAvailabilityRecalculationScheduler;
		$this->productAvailabilityCalculation = $productAvailabilityCalculation;
	}

	public function runAllScheduledRecalculations() {
		$this->productRowsIterator = null;
		// @codingStandardsIgnoreStart
		while ($this->runScheduledRecalculationsBatch()) {};
		// @codingStandardsIgnoreEnd
	}

	/**
	 * @return bool
	 */
	public function runScheduledRecalculationsBatch() {
		if ($this->productRowsIterator === null) {
			$this->productRowsIterator = $this->productAvailabilityRecalculationScheduler->getProductsIteratorForRecalculation();
		}

		for ($count = 0; $count < self::BATCH_SIZE; $count++) {
			$row = $this->productRowsIterator->next();
			if ($row === false) {
				$this->entityManagerFacade->clear();

				return false;
			}
			$this->recalculateAvailabilityForProduct($row[0]);
		}

		$this->entityManagerFacade->clear();

		return true;
	}

	public function runImmediateRecalculations() {
		$products = $this->productAvailabilityRecalculationScheduler->getProductsForImmediatelyRecalculation();
		foreach ($products as $product) {
			$this->recalculateAvailabilityForProduct($product);
		}
		$this->productAvailabilityRecalculationScheduler->cleanImmediatelyRecalculationSchedule();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 */
	private function recalculateAvailabilityForProduct(Product $product) {
		$calculatedAvailability = $this->productAvailabilityCalculation->calculateAvailability($product);
		$product->setCalculatedAvailability($calculatedAvailability);
		if ($product->isVariant()) {
			$this->recalculateAvailabilityForProduct($product->getMainVariant());
		}
		$this->em->flush($product);
	}

	/**
	 * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
	 */
	public function onKernelResponse(FilterResponseEvent $event) {
		if ($event->isMasterRequest()) {
			$this->runImmediateRecalculations();
		}
	}

}
