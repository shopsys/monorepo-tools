<?php

namespace Shopsys\ShopBundle\Model\Transport;

use Shopsys\ShopBundle\Model\Transport\Transport;
use Shopsys\ShopBundle\Model\Transport\TransportPriceRepository;

class TransportPriceEditFacade {

	/**
	 * @var \Shopsys\ShopBundle\Model\Transport\TransportPriceRepository
	 */
	private $transportPriceRepository;

	/**
	 * @param \Shopsys\ShopBundle\Model\Transport\TransportPriceRepository $transportPriceRepository
	 */
	public function __construct(TransportPriceRepository $transportPriceRepository) {
		$this->transportPriceRepository = $transportPriceRepository;
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Transport\Transport $transport
	 * @return \Shopsys\ShopBundle\Model\Transport\TransportPrice[]
	 */
	public function getAllByTransport(Transport $transport) {
		return $this->transportPriceRepository->getAllByTransport($transport);
	}

}
