<?php

namespace Shopsys\FrameworkBundle\Model\Pricing\Currency;

interface CurrencyDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyData
     */
    public function create(): CurrencyData;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyData
     */
    public function createFromCurrency(Currency $currency): CurrencyData;
}
