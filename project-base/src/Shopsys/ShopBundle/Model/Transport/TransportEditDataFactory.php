<?php

namespace Shopsys\ShopBundle\Model\Transport;

use Shopsys\ShopBundle\Model\Pricing\Vat\VatFacade;
use Shopsys\ShopBundle\Model\Transport\TransportEditData;
use Shopsys\ShopBundle\Model\Transport\TransportEditFacade;

class TransportEditDataFactory {

	/**
	 * @var \Shopsys\ShopBundle\Model\Transport\TransportEditFacade
	 */
	private $transportEditFacade;

	/**
	 * @var \Shopsys\ShopBundle\Model\Pricing\Vat\VatFacade
	 */
	private $vatFacade;

	public function __construct(
		TransportEditFacade $transportEditFacade,
		VatFacade $vatFacade
	) {
		$this->transportEditFacade = $transportEditFacade;
		$this->vatFacade = $vatFacade;
	}

	/**
	 * @return \Shopsys\ShopBundle\Model\Transport\TransportEditData
	 */
	public function createDefault() {
		$transportEditData = new TransportEditData();
		$transportEditData->transportData->vat = $this->vatFacade->getDefaultVat();

		return $transportEditData;
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Transport\Transport $transport
	 * @return \Shopsys\ShopBundle\Model\Transport\TransportEditData
	 */
	public function createFromTransport(Transport $transport) {
		$transportEditData = new TransportEditData();
		$transportData = new TransportData();
		$transportData->setFromEntity($transport, $this->transportEditFacade->getTransportDomainsByTransport($transport));
		$transportEditData->transportData = $transportData;

		foreach ($transport->getPrices() as $transportPrice) {
			$transportEditData->prices[$transportPrice->getCurrency()->getId()] = $transportPrice->getPrice();
		}

		return $transportEditData;
	}
}
