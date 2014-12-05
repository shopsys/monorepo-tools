<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Pricing\Currency\Currency;
use SS6\ShopBundle\Model\Pricing\Currency\CurrencyData;

class CurrencyDataFixture extends AbstractReferenceFixture {

	const CURRENCY_CZK = 'currency_czk';
	const CURRENCY_EUR = 'currency_eur';

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {

		$currencyData = new CurrencyData();

		$currencyData->setName('Česká koruna');
		$currencyData->setCode('CZK');
		$currencyData->setSymbol('Kč');
		$this->createCurrency($manager, self::CURRENCY_CZK, $currencyData);

		$currencyData->setName('Euro');
		$currencyData->setCode('EUR');
		$currencyData->setSymbol('€');
		$this->createCurrency($manager, self::CURRENCY_EUR, $currencyData);

		$manager->flush();
	}

	private function createCurrency(ObjectManager $manager, $referenceName, CurrencyData $currencyData) {
		$currency = new Currency($currencyData);
		$manager->persist($currency);
		$this->addReference($referenceName, $currency);
	}

}
