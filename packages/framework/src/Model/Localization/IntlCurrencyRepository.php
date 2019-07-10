<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Localization;

use CommerceGuys\Intl\Currency\Currency;
use CommerceGuys\Intl\Currency\CurrencyRepository as BaseCurrencyRepository;

class IntlCurrencyRepository extends BaseCurrencyRepository
{
    public const SUPPORTED_CURRENCY_CODES = [
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
        'BYN',
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
        'MRU',
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
        'STN',
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
        'UYW',
        'UZS',
        'VEF',
        'VES',
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
    public function get($currencyCode, $locale = null)
    {
        if (!$this->isSupportedCurrency($currencyCode)) {
            throw new \Shopsys\FrameworkBundle\Model\Localization\Exception\UnsupportedCurrencyException($currencyCode);
        }

        try {
            return parent::get($currencyCode, $locale);
        } catch (\CommerceGuys\Intl\Exception\UnknownCurrencyException $ex) {
            $legacyCurrencies = $this->getLegacyCurrenciesIndexedByCurrencyCodes();
            if (array_key_exists($currencyCode, $legacyCurrencies)) {
                return $legacyCurrencies[$currencyCode];
            }
            throw new \Shopsys\FrameworkBundle\Model\Localization\Exception\UndefinedLegacyCurrencyException($currencyCode);
        }
    }

    /**
     * {@inheritDoc}
     * @return \CommerceGuys\Intl\Currency\Currency[]
     */
    public function getAll($locale = null)
    {
        /** @var \CommerceGuys\Intl\Currency\Currency[] $intlCurrencies */
        $intlCurrencies = parent::getAll($locale);

        $supportedCurrencies = [];
        foreach ($intlCurrencies as $intlCurrency) {
            $currencyCode = $intlCurrency->getCurrencyCode();
            if ($this->isSupportedCurrency($currencyCode)) {
                $supportedCurrencies[$currencyCode] = $intlCurrency;
            }
        }

        return array_merge($this->getLegacyCurrenciesIndexedByCurrencyCodes(), $supportedCurrencies);
    }

    /**
     * @param string $currencyCode
     * @return bool
     */
    public function isSupportedCurrency(string $currencyCode): bool
    {
        return in_array($currencyCode, self::SUPPORTED_CURRENCY_CODES, true);
    }

    /**
     * @return \CommerceGuys\Intl\Currency\Currency[]
     */
    protected function getLegacyCurrenciesIndexedByCurrencyCodes(): array
    {
        return [
            'BYR' => new Currency([
                'currency_code' => 'BYR',
                'name' => 'Belarusian Ruble (2000–2016)',
                'locale' => 'en',
                'numeric_code' => '974',
            ]),
            'MRO' => new Currency([
                'currency_code' => 'MRO',
                'name' => 'Mauritanian Ouguiya',
                'locale' => 'en',
                'numeric_code' => '478',
            ]),
            'STD' => new Currency([
                'currency_code' => 'STD',
                'name' => 'São Tomé & Príncipe Dobra',
                'locale' => 'en',
                'numeric_code' => '678',
            ]),
            'VEF' => new Currency([
                'currency_code' => 'VEF',
                'name' => 'Venezuelan Bolívar',
                'locale' => 'en',
                'numeric_code' => '937',
            ]),
        ];
    }
}
