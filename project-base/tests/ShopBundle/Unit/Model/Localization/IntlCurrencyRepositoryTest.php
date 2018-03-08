<?php

namespace Tests\ShopBundle\Unit\Model\Localization;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Model\Localization\IntlCurrencyRepository;

class IntlCurrencyRepositoryTest extends TestCase
{
    /**
     * @dataProvider getSupportedCurrencyCodes
     */
    public function testGetSupportedCurrencies($currencyCode)
    {
        $intlCurrencyRepository = new IntlCurrencyRepository();
        $intlCurrencyRepository->get($currencyCode);
    }

    /**
     * @return string[][]
     */
    public function getSupportedCurrencyCodes()
    {
        $data = [];
        foreach (IntlCurrencyRepository::SUPPORTED_CURRENCY_CODES as $currencyCode) {
            $data[] = ['currencyCode' => $currencyCode];
        }

        return $data;
    }
}
