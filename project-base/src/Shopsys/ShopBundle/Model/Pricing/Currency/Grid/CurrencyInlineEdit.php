<?php

namespace Shopsys\ShopBundle\Model\Pricing\Currency\Grid;

use Shopsys\ShopBundle\Component\Grid\InlineEdit\AbstractGridInlineEdit;
use Shopsys\ShopBundle\Form\Admin\Pricing\Currency\CurrencyFormType;
use Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyData;
use Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyFacade;
use Symfony\Component\Form\FormFactory;

class CurrencyInlineEdit extends AbstractGridInlineEdit {

	/**
	 * @var \Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyFacade
	 */
	private $currencyFacade;

	/**
	 * @param \Symfony\Component\Form\FormFactory $formFactory
	 * @param \Shopsys\ShopBundle\Model\Pricing\Currency\Grid\CurrencyGridFactory $currencyGridFactory
	 * @param \Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyFacade $currencyFacade
	 */
	public function __construct(
		FormFactory $formFactory,
		CurrencyGridFactory $currencyGridFactory,
		CurrencyFacade $currencyFacade
	) {
		$this->currencyFacade = $currencyFacade;

		parent::__construct($formFactory, $currencyGridFactory);
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Pricing\Currency\currencyData $currencyData
	 * @return int
	 */
	protected function createEntityAndGetId($currencyData) {
		$currency = $this->currencyFacade->create($currencyData);

		return $currency->getId();
	}

	/**
	 * @param int $currencyId
	 * @param \Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyData $currencyData
	 */
	protected function editEntity($currencyId, $currencyData) {
		$this->currencyFacade->edit($currencyId, $currencyData);
	}

	/**
	 * @param int $currencyId
	 * @return \Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyData
	 */
	protected function getFormDataObject($currencyId = null) {
		$currencyData = new CurrencyData();

		if ($currencyId !== null) {
			$currencyId = (int)$currencyId;
			$currency = $this->currencyFacade->getById($currencyId);
			$currencyData->setFromEntity($currency);
		}

		return $currencyData;
	}

	/**
	 * @param int $currencyId
	 * @return \Shopsys\ShopBundle\Form\Admin\Pricing\Currency\CurrencyFormType
	 */
	protected function getFormType($currencyId) {
		if ($currencyId !== null) {
			$currency = $this->currencyFacade->getById($currencyId);
			if ($this->currencyFacade->isDefaultCurrency($currency)) {
				return new CurrencyFormType(CurrencyFormType::EXCHANGE_RATE_IS_READ_ONLY);
			} else {
				return new CurrencyFormType(CurrencyFormType::EXCHANGE_RATE_IS_NOT_READ_ONLY);
			}
		} else {
			return new CurrencyFormType(CurrencyFormType::EXCHANGE_RATE_IS_NOT_READ_ONLY);
		}
	}
}
