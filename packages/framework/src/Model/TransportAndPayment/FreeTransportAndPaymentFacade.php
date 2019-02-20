<?php

namespace Shopsys\FrameworkBundle\Model\TransportAndPayment;

use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Pricing\PricingSetting;

class FreeTransportAndPaymentFacade
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\PricingSetting
     */
    protected $pricingSetting;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\PricingSetting $pricingSetting
     */
    public function __construct(PricingSetting $pricingSetting)
    {
        $this->pricingSetting = $pricingSetting;
    }

    /**
     * @param int $domainId
     * @return bool
     */
    public function isActive($domainId)
    {
        return $this->getFreeTransportAndPaymentPriceLimitOnDomain($domainId) !== null;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $productsPriceWithVat
     * @param int $domainId
     * @return bool
     */
    public function isFree(Money $productsPriceWithVat, $domainId)
    {
        $freeTransportAndPaymentPriceLimit = $this->getFreeTransportAndPaymentPriceLimitOnDomain($domainId);
        if ($freeTransportAndPaymentPriceLimit === null) {
            return false;
        }

        return $productsPriceWithVat->isGreaterThanOrEqualTo($freeTransportAndPaymentPriceLimit);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $productsPriceWithVat
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Component\Money\Money
     */
    public function getRemainingPriceWithVat(Money $productsPriceWithVat, $domainId): Money
    {
        if (!$this->isFree($productsPriceWithVat, $domainId) && $this->isActive($domainId)) {
            return $this->getFreeTransportAndPaymentPriceLimitOnDomain($domainId)->subtract($productsPriceWithVat);
        }

        return Money::fromInteger(0);
    }

    /**
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Component\Money\Money|null
     */
    protected function getFreeTransportAndPaymentPriceLimitOnDomain($domainId): ?Money
    {
        return $this->pricingSetting->getFreeTransportAndPaymentPriceLimit($domainId);
    }
}
