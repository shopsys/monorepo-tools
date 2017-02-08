<?php

namespace SS6\ShopBundle\Form\Admin\Transport;

use SS6\ShopBundle\Form\Admin\Transport\TransportFormTypeFactory;
use SS6\ShopBundle\Model\Pricing\Currency\CurrencyFacade;

class TransportEditFormTypeFactory {

	/**
	 * @var \SS6\ShopBundle\Form\Admin\Transport\TransportFormTypeFactory
	 */
	private $transportFormTypeFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Currency\CurrencyFacade
	 */
	private $currencyFacade;

	public function __construct(TransportFormTypeFactory $transportFormTypeFactory, CurrencyFacade $currencyFacade) {
		$this->transportFormTypeFactory = $transportFormTypeFactory;
		$this->currencyFacade = $currencyFacade;
	}

	/**
	 * @return \SS6\ShopBundle\Form\Admin\Transport\TransportFormType
	 */
	public function create() {
		$currencies = $this->currencyFacade->getAll();

		return new TransportEditFormType($this->transportFormTypeFactory, $currencies);
	}

}
