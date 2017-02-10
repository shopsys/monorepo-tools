<?php

namespace Shopsys\ShopBundle\Tests\Unit\Model\Localization;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Model\Localization\IntlCurrencyRepository;

class IntlCurrencyRepositoryTest extends PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider getSupportedCurrencyCodes
     */
    public function testGetSupportedCurrencies($currencyCode) {
        $intlCurrencyRepository = new IntlCurrencyRepository();
        $intlCurrencyRepository->get($currencyCode);
    }

    /**
     * @return string[][]
     */
    public function getSupportedCurrencyCodes() {
        $data = [];
        foreach (IntlCurrencyRepository::SUPPORTED_CURRENCY_CODES as $currencyCode) {
            $data[] = ['currencyCode' => $currencyCode];
        }

        return $data;
    }
}
