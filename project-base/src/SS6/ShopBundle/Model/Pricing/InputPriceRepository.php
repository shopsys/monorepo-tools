<?php

namespace SS6\ShopBundle\Model\Pricing;

use Closure;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Payment\PriceCalculation as PaymentPriceCalculation;
use SS6\ShopBundle\Model\Pricing\InputPriceCalculation;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\PriceCalculation as ProductPriceCalculation;
use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Transport\PriceCalculation as TransportPriceCalculation;

class InputPriceRepository {

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
	 * @var \SS6\ShopBundle\Model\Product\PriceCalculation
	 */
	private $productPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\PriceCalculation
	 */
	private $paymentPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\PriceCalculation
	 */
	private $transportPriceCalculation;

	/**
	 * @param EntityManager $em
	 * @param \SS6\ShopBundle\Model\Pricing\InputPriceCalculation $inputPriceCalculation
	 * @param \SS6\ShopBundle\Model\Product\PriceCalculation $productPriceCalculation
	 * @param \SS6\ShopBundle\Model\Payment\PriceCalculation $paymentPriceCalculation
	 * @param \SS6\ShopBundle\Model\Transport\PriceCalculation $transportPriceCalculation
	 */
	public function __construct(
		EntityManager $em,
		InputPriceCalculation $inputPriceCalculation,
		ProductPriceCalculation $productPriceCalculation,
		PaymentPriceCalculation $paymentPriceCalculation,
		TransportPriceCalculation $transportPriceCalculation
	) {
		$this->em = $em;
		$this->inputPriceCalculation = $inputPriceCalculation;
		$this->productPriceCalculation = $productPriceCalculation;
		$this->paymentPriceCalculation = $paymentPriceCalculation;
		$this->transportPriceCalculation = $transportPriceCalculation;
	}

	public function recalculateToInputPricesWithoutVat() {
		$this->recalculateInputPriceForNewType(PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT);
	}

	public function recalculateToInputPricesWithVat() {
		$this->recalculateInputPriceForNewType(PricingSetting::INPUT_PRICE_TYPE_WITH_VAT);
	}

	/**
	 * @param string $newInputPriceType
	 * @throws \SS6\ShopBundle\Model\Pricing\Exception\InvalidInputPriceTypeException
	 */
	private function recalculateInputPriceForNewType($newInputPriceType) {
		$this->recalculateProductsInputPriceForNewType($newInputPriceType);
		$this->recalculateTransportsInputPriceForNewType($newInputPriceType);
		$this->recalculatePaymentsInputPriceForNewType($newInputPriceType);
	}

	/**
	 * @param string $toInputPriceType
	 * @throws \SS6\ShopBundle\Model\Pricing\Exception\InvalidInputPriceTypeException
	 */
	private function recalculateProductsInputPriceForNewType($toInputPriceType) {
		$query = $this->em->createQueryBuilder()
			->select('p')
			->from(Product::class, 'p')
			->getQuery();

		$this->batchProcessQuery($query, function (Product $product) use ($toInputPriceType) {
			$productPrice = $this->productPriceCalculation->calculatePrice($product);

			if ($toInputPriceType === PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT) {
				$product->setPrice($this->inputPriceCalculation->getInputPriceWithoutVat(
					$productPrice->getPriceWithVat(),
					$product->getVat()
				));
			} elseif ($toInputPriceType === PricingSetting::INPUT_PRICE_TYPE_WITH_VAT) {
				$product->setPrice($productPrice->getPriceWithVat());
			} else {
				throw new \SS6\ShopBundle\Model\Pricing\Exception\InvalidInputPriceTypeException();
			}
		});
	}

	/**
	 * @param string $toInputPriceType
	 * @throws \SS6\ShopBundle\Model\Pricing\Exception\InvalidInputPriceTypeException
	 */
	private function recalculateTransportsInputPriceForNewType($toInputPriceType) {
		$query = $this->em->createQueryBuilder()
			->select('p')
			->from(Payment::class, 'p')
			->getQuery();

		$this->batchProcessQuery($query, function (Payment $payment) use ($toInputPriceType) {
			$paymentPrice = $this->paymentPriceCalculation->calculatePrice($payment);

			if ($toInputPriceType === PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT) {
				$payment->setPrice($this->inputPriceCalculation->getInputPriceWithoutVat(
					$paymentPrice->getPriceWithVat(),
					$payment->getVat()
				));
			} elseif ($toInputPriceType === PricingSetting::INPUT_PRICE_TYPE_WITH_VAT) {
				$payment->setPrice($paymentPrice->getPriceWithVat());
			} else {
				throw new \SS6\ShopBundle\Model\Pricing\Exception\InvalidInputPriceTypeException();
			}
		});
	}

	/**
	 * @param string $toInputPriceType
	 * @throws \SS6\ShopBundle\Model\Pricing\Exception\InvalidInputPriceTypeException
	 */
	private function recalculatePaymentsInputPriceForNewType($toInputPriceType) {
		$query = $this->em->createQueryBuilder()
			->select('t')
			->from(Transport::class, 't')
			->getQuery();

		$this->batchProcessQuery($query, function (Transport $transport) use ($toInputPriceType) {
			$transportPrice = $this->transportPriceCalculation->calculatePrice($transport);

			if ($toInputPriceType === PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT) {
				$transport->setPrice($this->inputPriceCalculation->getInputPriceWithoutVat(
					$transportPrice->getPriceWithVat(),
					$transport->getVat()
				));
			} elseif ($toInputPriceType === PricingSetting::INPUT_PRICE_TYPE_WITH_VAT) {
				$transport->setPrice($transportPrice->getPriceWithVat());
			} else {
				throw new \SS6\ShopBundle\Model\Pricing\Exception\InvalidInputPriceTypeException();
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
