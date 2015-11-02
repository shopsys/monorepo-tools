<?php

namespace SS6\ShopBundle\Form\Admin\Payment;

use SS6\ShopBundle\Model\Pricing\Vat\VatRepository;
use SS6\ShopBundle\Model\Transport\TransportRepository;

class PaymentFormTypeFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Transport\TransportRepository
	 */
	private $transportRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\VatRepository
	 */
	private $vatRepository;

	/**
	 * @param \SS6\ShopBundle\Model\Transport\TransportRepository $transportRepository
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\VatRepository $vatRepository
	 */
	public function __construct(
		TransportRepository $transportRepository,
		VatRepository $vatRepository
	) {
		$this->transportRepository = $transportRepository;
		$this->vatRepository = $vatRepository;
	}

	/**
	 * @return \SS6\ShopBundle\Form\Admin\Payment\PaymentFormType
	 */
	public function create() {
		$allTransports = $this->transportRepository->getAll();
		$vats = $this->vatRepository->getAll();

		return new PaymentFormType($allTransports, $vats);
	}

}
