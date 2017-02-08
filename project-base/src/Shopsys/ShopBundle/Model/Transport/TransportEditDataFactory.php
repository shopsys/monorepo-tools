<?php

namespace SS6\ShopBundle\Model\Transport;

use SS6\ShopBundle\Model\Pricing\Vat\VatFacade;
use SS6\ShopBundle\Model\Transport\TransportEditData;
use SS6\ShopBundle\Model\Transport\TransportEditFacade;

class TransportEditDataFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Transport\TransportEditFacade
	 */
	private $transportEditFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\TransportPriceEditFacade
	 */
	private $transportPriceEditFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\VatFacade
	 */
	private $vatFacade;

	public function __construct(
		TransportEditFacade $transportEditFacade,
		TransportPriceEditFacade $transportPriceEditFacade,
		VatFacade $vatFacade
	) {
		$this->transportEditFacade = $transportEditFacade;
		$this->transportPriceEditFacade = $transportPriceEditFacade;
		$this->vatFacade = $vatFacade;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Transport\TransportEditData
	 */
	public function createDefault() {
		$transportEditData = new TransportEditData();
		$transportEditData->transportData->vat = $this->vatFacade->getDefaultVat();

		return $transportEditData;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @return \SS6\ShopBundle\Model\Transport\TransportEditData
	 */
	public function createFromTransport(Transport $transport) {
		$transportEditData = new TransportEditData();
		$transportData = new TransportData();
		$transportData->setFromEntity($transport, $this->transportEditFacade->getTransportDomainsByTransport($transport));
		$transportEditData->transportData = $transportData;

		foreach ($this->transportPriceEditFacade->getAllByTransport($transport) as $transportPrice) {
			$transportEditData->prices[$transportPrice->getCurrency()->getId()] = $transportPrice->getPrice();
		}

		return $transportEditData;
	}
}
