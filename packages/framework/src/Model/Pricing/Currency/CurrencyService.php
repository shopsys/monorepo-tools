<?php

namespace Shopsys\FrameworkBundle\Model\Pricing\Currency;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Pricing\PricingSetting;

class CurrencyService
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyData $currencyData
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency
     */
    public function create(CurrencyData $currencyData)
    {
        return new Currency($currencyData);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyData $currencyData
     * @param bool $isDefaultCurrency
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency
     */
    public function edit(Currency $currency, CurrencyData $currencyData, $isDefaultCurrency)
    {
        $currency->edit($currencyData);
        if ($isDefaultCurrency) {
            $currency->setExchangeRate(Currency::DEFAULT_EXCHANGE_RATE);
        } else {
            $currency->setExchangeRate($currencyData->exchangeRate);
        }

        return $currency;
    }

    /**
     * @param int $defaultCurrencyId
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency[] $currenciesUsedInOrders
     * @param \Shopsys\FrameworkBundle\Model\Pricing\PricingSetting $pricingSetting
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     * @return int[]
     */
    public function getNotAllowedToDeleteCurrencyIds(
        $defaultCurrencyId,
        array $currenciesUsedInOrders,
        PricingSetting $pricingSetting,
        Domain $domain
    ) {
        $notAllowedToDeleteCurrencyIds = [$defaultCurrencyId];
        foreach ($domain->getAll() as $domainConfig) {
            $notAllowedToDeleteCurrencyIds[] = $pricingSetting->getDomainDefaultCurrencyIdByDomainId($domainConfig->getId());
        }
        foreach ($currenciesUsedInOrders as $currency) {
            $notAllowedToDeleteCurrencyIds[] = $currency->getId();
        }

        return array_unique($notAllowedToDeleteCurrencyIds);
    }
}
