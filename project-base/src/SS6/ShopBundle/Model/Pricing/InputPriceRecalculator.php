<?php

namespace SS6\ShopBundle\Model\Pricing;

use Closure;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Payment\PaymentPriceCalculation;
use SS6\ShopBundle\Model\Pricing\InputPriceCalculation;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculation;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductService;
use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Transport\TransportPriceCalculation;

class InputPriceRecalculator {

	const BATCH_SIZE = 500;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\InputPriceCalculation
	 */
	private $inputPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculation
	 */
	private $productPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\PaymentPriceCalculation
	 */
	private $paymentPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\TransportPriceCalculation
	 */
	private $transportPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductService
	 */
	private $productService;

	/**
	 * @param EntityManager $em
	 * @param \SS6\ShopBundle\Model\Pricing\InputPriceCalculation $inputPriceCalculation
	 * @param \SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculation $productPriceCalculation
	 * @param \SS6\ShopBundle\Model\Payment\PaymentPriceCalculation $paymentPriceCalculation
	 * @param \SS6\ShopBundle\Model\Transport\TransportPriceCalculation $transportPriceCalculation
	 */
	public function __construct(
		EntityManager $em,
		InputPriceCalculation $inputPriceCalculation,
		ProductPriceCalculation $productPriceCalculation,
		PaymentPriceCalculation $paymentPriceCalculation,
		TransportPriceCalculation $transportPriceCalculation,
		ProductService $productService
	) {
		$this->em = $em;
		$this->inputPriceCalculation = $inputPriceCalculation;
		$this->productPriceCalculation = $productPriceCalculation;
		$this->paymentPriceCalculation = $paymentPriceCalculation;
		$this->transportPriceCalculation = $transportPriceCalculation;
		$this->productService = $productService;
	}

	public function recalculateToInputPricesWithoutVat() {
		$this->recalculateInputPriceForNewType(PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT);
	}

	public function recalculateToInputPricesWithVat() {
		$this->recalculateInputPriceForNewType(PricingSetting::INPUT_PRICE_TYPE_WITH_VAT);
	}

	/**
	 * @param string $newInputPriceType
	 */
	private function recalculateInputPriceForNewType($newInputPriceType) {
		$this->recalculateProductsInputPriceForNewType($newInputPriceType);
		$this->recalculateTransportsInputPriceForNewType($newInputPriceType);
		$this->recalculatePaymentsInputPriceForNewType($newInputPriceType);
	}

	/**
	 * @param string $toInputPriceType
	 */
	private function recalculateProductsInputPriceForNewType($toInputPriceType) {
		$query = $this->em->createQueryBuilder()
			->select('p')
			->from(Product::class, 'p')
			->getQuery();

		$this->batchProcessQuery($query, function (Product $product) use ($toInputPriceType) {
			$productPrice = $this->productPriceCalculation->calculateBasePrice($product);

			$newInputPrice = $this->inputPriceCalculation->getInputPrice(
				$toInputPriceType,
				$productPrice->getPriceWithVat(),
				$product->getVat()->getPercent()
			);

			$this->productService->setInputPrice($product, $newInputPrice);
		});
	}

	/**
	 * @param string $toInputPriceType
	 */
	private function recalculatePaymentsInputPriceForNewType($toInputPriceType) {
		$query = $this->em->createQueryBuilder()
			->select('p')
			->from(Payment::class, 'p')
			->getQuery();

		$this->batchProcessQuery($query, function (Payment $payment) use ($toInputPriceType) {
			foreach ($payment->getPrices() as $paymentInputPrice) {
				$paymentPrice = $this->paymentPriceCalculation->calculateIndependentPrice(
					$payment,
					$paymentInputPrice->getCurrency()
				);

				$newInputPrice = $this->inputPriceCalculation->getInputPrice(
					$toInputPriceType,
					$paymentPrice->getPriceWithVat(),
					$payment->getVat()->getPercent()
				);

				$paymentInputPrice->setPrice($newInputPrice);
			}
		});
	}

	/**
	 * @param string $toInputPriceType
	 */
	private function recalculateTransportsInputPriceForNewType($toInputPriceType) {
		$query = $this->em->createQueryBuilder()
			->select('t')
			->from(Transport::class, 't')
			->getQuery();

		$this->batchProcessQuery($query, function (Transport $transport) use ($toInputPriceType) {
			foreach ($transport->getPrices() as $transportInputPrice) {
				$transportPrice = $this->transportPriceCalculation->calculateIndependentPrice(
					$transport,
					$transportInputPrice->getCurrency()
				);

				$newInputPrice = $this->inputPriceCalculation->getInputPrice(
					$toInputPriceType,
					$transportPrice->getPriceWithVat(),
					$transport->getVat()->getPercent()
				);

				$transportInputPrice->setPrice($newInputPrice);
			}
		});
	}

	/**
	 * @param \Doctrine\ORM\Query $query
	 * @param \Closure $callback
	 */
	private function batchProcessQuery(Query $query, Closure $callback) {
		$iteration = 0;

		foreach ($query->iterate() as $row) {
			$iteration++;
			$object = $row[0];

			$callback($object);

			if (($iteration % self::BATCH_SIZE) == 0) {
				$this->em->flush();
			}
		}

		$this->em->flush();
	}

}
