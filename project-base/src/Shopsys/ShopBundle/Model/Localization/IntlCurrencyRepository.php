<?php

namespace Shopsys\ShopBundle\Model\Localization;

use CommerceGuys\Intl\Currency\CurrencyRepository as BaseCurrencyRepository;

class IntlCurrencyRepository extends BaseCurrencyRepository
{

    const SUPPORTED_CURRENCY_CODES = [
        'AED',
        'AFN',
        'ALL',
        'AMD',
        'ANG',
        'AOA',
        'ARS',
        'AUD',
        'AWG',
        'AZN',
        'BAM',
        'BBD',
        'BDT',
        'BGN',
        'BHD',
        'BIF',
        'BMD',
        'BND',
        'BOB',
        'BRL',
        'BSD',
        'BTN',
        'BWP',
        'BYR',
        'BZD',
        'CAD',
        'CDF',
        'CLP',
        'CNY',
        'COP',
        'CRC',
        'CUC',
        'CUP',
        'CVE',
        'CZK',
        'DJF',
        'DKK',
        'DOP',
        'DZD',
        'EGP',
        'ERN',
        'ETB',
        'EUR',
        'FJD',
        'FKP',
        'GBP',
        'GEL',
        'GHS',
        'GIP',
        'GMD',
        'GNF',
        'GTQ',
        'GYD',
        'HKD',
        'HNL',
        'HRK',
        'HTG',
        'HUF',
        'CHF',
        'IDR',
        'ILS',
        'INR',
        'IQD',
        'IRR',
        'ISK',
        'JMD',
        'JOD',
        'JPY',
        'KES',
        'KGS',
        'KHR',
        'KMF',
        'KPW',
        'KRW',
        'KWD',
        'KYD',
        'KZT',
        'LAK',
        'LBP',
        'LKR',
        'LRD',
        'LSL',
        'LYD',
        'MAD',
        'MDL',
        'MGA',
        'MKD',
        'MMK',
        'MNT',
        'MOP',
        'MRO',
        'MUR',
        'MVR',
        'MWK',
        'MXN',
        'MYR',
        'MZN',
        'NAD',
        'NGN',
        'NIO',
        'NOK',
        'NPR',
        'NZD',
        'OMR',
        'PAB',
        'PEN',
        'PGK',
        'PHP',
        'PKR',
        'PLN',
        'PYG',
        'QAR',
        'RON',
        'RSD',
        'RUB',
        'RWF',
        'SAR',
        'SBD',
        'SCR',
        'SDG',
        'SEK',
        'SGD',
        'SHP',
        'SLL',
        'SOS',
        'SRD',
        'SSP',
        'STD',
        'SVC',
        'SYP',
        'SZL',
        'THB',
        'TJS',
        'TMT',
        'TND',
        'TOP',
        'TRY',
        'TTD',
        'TWD',
        'TZS',
        'UAH',
        'UGX',
        'USD',
        'UYU',
        'UZS',
        'VEF',
        'VND',
        'VUV',
        'WST',
        'XAF',
        'XCD',
        'XOF',
        'XPF',
        'YER',
        'ZAR',
        'ZMW',
        'ZWL',
    ];

    /**
     * {@inheritDoc}
     */
    public function get($currencyCode, $locale = null, $fallbackLocale = null) {
        if (!$this->isSupportedCurrency($currencyCode)) {
            throw new \Shopsys\ShopBundle\Model\Localization\Exception\UnsupportedCurrencyException($currencyCode);
        }

        $intlCurrency = parent::get($currencyCode, $locale, $fallbackLocale);

        return $intlCurrency;
    }

    /**
     * {@inheritDoc}
     * @return \CommerceGuys\Intl\Currency\CurrencyInterface[]
     */
    public function getAll($locale = null, $fallbackLocale = null) {
        $intlCurrencies = parent::getAll($locale, $fallbackLocale);
        /* @var $intlCurrencies \CommerceGuys\Intl\Currency\CurrencyInterface[] */

        $supportedCurrencies = [];
        foreach ($intlCurrencies as $intlCurrency) {
            if ($this->isSupportedCurrency($intlCurrency->getCurrencyCode())) {
                $supportedCurrencies[] = $intlCurrency;
            }
        }

        return $supportedCurrencies;
    }

    /**
     * @param string $currencyCode
     * @return bool
     */
    public function isSupportedCurrency($currencyCode) {
        return in_array($currencyCode, self::SUPPORTED_CURRENCY_CODES, true);
    }
}
