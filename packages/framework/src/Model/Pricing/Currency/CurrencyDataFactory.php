<?php

namespace Shopsys\FrameworkBundle\Model\Pricing\Currency;

class CurrencyDataFactory implements CurrencyDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyData
     */
    public function create(): CurrencyData
    {
        return new CurrencyData();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyData
     */
    public function createFromCurrency(Currency $currency): CurrencyData
    {
        $currencyData = new CurrencyData();
        $this->fillFromCurrency($currencyData, $currency);

        return $currencyData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyData $currencyData
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency
     */
    protected function fillFromCurrency(CurrencyData $currencyData, Currency $currency)
    {
        $currencyData->name = $currency->getName();
        $currencyData->code = $currency->getCode();
        $currencyData->exchangeRate = $currency->getExchangeRate();
    }
}
