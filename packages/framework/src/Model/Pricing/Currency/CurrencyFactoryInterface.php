<?php

namespace Shopsys\FrameworkBundle\Model\Pricing\Currency;

interface CurrencyFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyData $data
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency
     */
    public function create(CurrencyData $data): Currency;
}
