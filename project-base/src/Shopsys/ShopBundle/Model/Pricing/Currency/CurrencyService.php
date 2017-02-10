<?php

namespace Shopsys\ShopBundle\Model\Pricing\Currency;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Model\Pricing\Currency\Currency;
use Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyData;
use Shopsys\ShopBundle\Model\Pricing\PricingSetting;

class CurrencyService {

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyData $currencyData
     * @return \Shopsys\ShopBundle\Model\Pricing\Currency\Currency
     */
    public function create(CurrencyData $currencyData) {
        return new Currency($currencyData);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\Currency\Currency $currency
     * @param \Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyData $currencyData
     * @param bool $isDefaultCurrency
     * @return \Shopsys\ShopBundle\Model\Pricing\Currency\Currency
     */
    public function edit(Currency $currency, CurrencyData $currencyData, $isDefaultCurrency) {
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
     * @param \Shopsys\ShopBundle\Model\Pricing\Currency\Currency[] $currenciesUsedInOrders
     * @param \Shopsys\ShopBundle\Model\Pricing\PricingSetting $pricingSetting
     * @param \Shopsys\ShopBundle\Component\Domain\Domain $domain
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
