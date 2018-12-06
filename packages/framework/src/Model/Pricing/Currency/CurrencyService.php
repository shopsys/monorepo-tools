<?php

namespace Shopsys\FrameworkBundle\Model\Pricing\Currency;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Pricing\PricingSetting;

class CurrencyService
{
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
