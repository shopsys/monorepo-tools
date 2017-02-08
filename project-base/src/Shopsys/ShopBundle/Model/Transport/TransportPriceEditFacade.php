<?php

namespace SS6\ShopBundle\Model\Transport;

use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Transport\TransportPriceRepository;

class TransportPriceEditFacade {

	/**
	 * @var \SS6\ShopBundle\Model\Transport\TransportPriceRepository
	 */
	private $transportPriceRepository;

	/**
	 * @param \SS6\ShopBundle\Model\Transport\TransportPriceRepository $transportPriceRepository
	 */
	public function __construct(TransportPriceRepository $transportPriceRepository) {
		$this->transportPriceRepository = $transportPriceRepository;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @return \SS6\ShopBundle\Model\Transport\TransportPrice[]
	 */
	public function getAllByTransport(Transport $transport) {
		return $this->transportPriceRepository->getAllByTransport($transport);
	}

}
