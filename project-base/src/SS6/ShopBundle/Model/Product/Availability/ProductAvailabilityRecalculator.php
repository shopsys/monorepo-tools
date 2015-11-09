<?php

namespace SS6\ShopBundle\Model\Product\Availability;

use Doctrine\ORM\EntityManager;
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

	/**
	 * @return int
	 */
	public function runAllScheduledRecalculations() {
		return $this->runScheduledRecalculationsWhile(function () {
			return true;
		});
	}

	/**
	 * @param callable $canRunCallback
	 * @return int
	 */
	public function runScheduledRecalculationsWhile(callable $canRunCallback) {
		$productRows = $this->productAvailabilityRecalculationScheduler->getProductsIteratorForRecalculation();
		$count = 0;

		foreach ($productRows as $row) {
			if (!$canRunCallback()) {
				return $count;
			}
			$this->recalculateAvailabilityForProduct($row[0]);
			$count++;
			if ($count % self::BATCH_SIZE === 0) {
				$this->em->clear();
			}
		}
		$this->em->clear();

		return $count;
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
		$calculatedAvailability = $this->productAvailabilityCalculation->getCalculatedAvailability($product);
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
