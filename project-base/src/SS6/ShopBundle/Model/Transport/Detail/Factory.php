<?php

namespace SS6\ShopBundle\Model\Transport\Detail;

use SS6\ShopBundle\Model\Payment\PaymentRepository;
use SS6\ShopBundle\Model\Transport\PriceCalculation;
use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Transport\VisibilityCalculation;

class Factory {

	/**
	 * @var \SS6\ShopBundle\Model\Transport\PriceCalculation
	 */
	private $priceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\PaymentRepository
	 */
	private $paymentRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\VisibilityCalculation
	 */
	private $visibilityCalculation;

	/**
	 * @param \SS6\ShopBundle\Model\Transport\PriceCalculation $priceCalculation
	 * @param \SS6\ShopBundle\Model\Payment\PaymentRepository $paymentRepository
	 * @param \SS6\ShopBundle\Model\Transport\VisibilityCalculation $visibilityCalculation
	 */
	public function __construct(
		PriceCalculation $priceCalculation,
		PaymentRepository $paymentRepository,
		VisibilityCalculation $visibilityCalculation
	) {
		$this->priceCalculation = $priceCalculation;
		$this->paymentRepository = $paymentRepository;
		$this->visibilityCalculation = $visibilityCalculation;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @return \SS6\ShopBundle\Model\Transport\Detail\Detail
	 */
	public function createDetailForTransport(Transport $transport) {
		$allPayments = $this->paymentRepository->findAll();

		return new Detail(
			$transport,
			$this->getPrice($transport),
			$this->isVisible($transport, $allPayments)
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport[] $transports
	 * @return \SS6\ShopBundle\Model\Transport\Detail\Detail[]
	 */
	public function createDetailsForTransports(array $transports) {
		$details = array();

		$allPayments = $this->paymentRepository->findAll();

		foreach ($transports as $transport) {
			$details[] = new Detail(
				$transport,
				$this->getPrice($transport),
				$this->isVisible($transport, $allPayments)
			);
		}

		return $details;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	private function getPrice(Transport $transport) {
		return $this->priceCalculation->calculatePrice($transport);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @param \SS6\ShopBundle\Model\Payment\Payment[] $allPayments
	 * @return boolean
	 */
	private function isVisible(Transport $transport, array $allPayments) {
		return $this->visibilityCalculation->isVisible($transport, $allPayments);
	}

}
