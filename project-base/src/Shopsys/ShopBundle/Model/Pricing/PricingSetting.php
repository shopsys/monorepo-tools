<?php

namespace Shopsys\ShopBundle\Model\Pricing;

use Shopsys\ShopBundle\Component\Setting\Setting;
use Shopsys\ShopBundle\Model\Pricing\Currency\Currency;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;

class PricingSetting {

    const INPUT_PRICE_TYPE = 'inputPriceType';
    const ROUNDING_TYPE = 'roundingType';
    const DEFAULT_CURRENCY = 'defaultCurrencyId';
    const DEFAULT_DOMAIN_CURRENCY = 'defaultDomainCurrencyId';
    const FREE_TRANSPORT_AND_PAYMENT_PRICE_LIMIT = 'freeTransportAndPaymentPriceLimit';

    const INPUT_PRICE_TYPE_WITH_VAT = 1;
    const INPUT_PRICE_TYPE_WITHOUT_VAT = 2;

    const ROUNDING_TYPE_HUNDREDTHS = 1;
    const ROUNDING_TYPE_FIFTIES = 2;
    const ROUNDING_TYPE_INTEGER = 3;

    /**
     * @var \Shopsys\ShopBundle\Component\Setting\Setting
     */
    private $setting;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler
     */
    private $productPriceRecalculationScheduler;

    public function __construct(
        Setting $setting,
        ProductPriceRecalculationScheduler $productPriceRecalculationScheduler
    ) {
        $this->setting = $setting;
        $this->productPriceRecalculationScheduler = $productPriceRecalculationScheduler;
    }

    /**
     * @return int
     */
    public function getInputPriceType() {
        return $this->setting->get(self::INPUT_PRICE_TYPE);
    }

    /**
     * @return int
     */
    public function getRoundingType() {
        return $this->setting->get(self::ROUNDING_TYPE);
    }

    /**
     * @return int
     */
    public function getDefaultCurrencyId() {
        return $this->setting->get(self::DEFAULT_CURRENCY);
    }

    /**
     * @param int $domainId
     * @return int
     */
    public function getDomainDefaultCurrencyIdByDomainId($domainId) {
        return $this->setting->getForDomain(self::DEFAULT_DOMAIN_CURRENCY, $domainId);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\Currency\Currency $currency
     */
    public function setDefaultCurrency(Currency $currency) {
        $currency->setExchangeRate(Currency::DEFAULT_EXCHANGE_RATE);
        $this->setting->set(self::DEFAULT_CURRENCY, $currency->getId());
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\Currency\Currency $currency
     * @param int $domainId
     */
    public function setDomainDefaultCurrency(Currency $currency, $domainId) {
        $this->setting->setForDomain(self::DEFAULT_DOMAIN_CURRENCY, $currency->getId(), $domainId);
    }

    /**
     * @param int $roundingType
     */
    public function setRoundingType($roundingType) {
        if (!in_array($roundingType, $this->getRoundingTypes())) {
            throw new \Shopsys\ShopBundle\Model\Pricing\Exception\InvalidRoundingTypeException(
                sprintf('Rounding type %s is not valid', $roundingType)
            );
        }

        $this->setting->set(self::ROUNDING_TYPE, $roundingType);
        $this->productPriceRecalculationScheduler->scheduleAllProductsForDelayedRecalculation();
    }

    /**
     * @param int $domainId
     * @return string|null
     */
    public function getFreeTransportAndPaymentPriceLimit($domainId) {
        return $this->setting->getForDomain(self::FREE_TRANSPORT_AND_PAYMENT_PRICE_LIMIT, $domainId);
    }

    /**
     * @param int $domainId
     * @param string|null $priceLimit
     */
    public function setFreeTransportAndPaymentPriceLimit($domainId, $priceLimit = null) {
        $this->setting->setForDomain(self::FREE_TRANSPORT_AND_PAYMENT_PRICE_LIMIT, $priceLimit, $domainId);
    }

    /**
     * @return array
     */
    public static function getInputPriceTypes() {
        return [
            self::INPUT_PRICE_TYPE_WITHOUT_VAT,
            self::INPUT_PRICE_TYPE_WITH_VAT,
        ];
    }

    /**
     * @return array
     */
    public static function getRoundingTypes() {
        return [
            self::ROUNDING_TYPE_HUNDREDTHS,
            self::ROUNDING_TYPE_FIFTIES,
            self::ROUNDING_TYPE_INTEGER,
        ];
    }

}
