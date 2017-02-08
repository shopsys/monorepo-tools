<?php

namespace SS6\ShopBundle\Form\Admin\Transport;

use SS6\ShopBundle\Model\Pricing\Vat\VatRepository;

class TransportFormTypeFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\VatRepository
	 */
	private $vatRepository;

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\VatRepository $vatRepository
	 */
	public function __construct(VatRepository $vatRepository) {
		$this->vatRepository = $vatRepository;
	}

	/**
	 * @return \SS6\ShopBundle\Form\Admin\Transport\TransportFormType
	 */
	public function create() {
		$vats = $this->vatRepository->getAll();

		return new TransportFormType($vats);
	}

}
