<?php

namespace Shopsys\ShopBundle\Tests\Twig;

use CommerceGuys\Intl\NumberFormat\NumberFormatRepositoryInterface;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Model\Localization\IntlCurrencyRepository;
use Shopsys\ShopBundle\Model\Localization\Localization;
use Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyFacade;
use Shopsys\ShopBundle\Tests\Test\FunctionalTestCase;
use Shopsys\ShopBundle\Twig\PriceExtension;

class PriceExtensionTest extends FunctionalTestCase {

    const NBSP = "\xc2\xa0";

    public function priceFilterDataProvider() {
        return [
            ['input' => '12', 'domainId' => 1, 'result' => '12,00' . self::NBSP . 'Kč'],
            ['input' => '12.00', 'domainId' => 1, 'result' => '12,00' . self::NBSP . 'Kč'],
            ['input' => '12.600', 'domainId' => 1, 'result' => '12,60' . self::NBSP . 'Kč'],
            ['input' => '12.630000', 'domainId' => 1, 'result' => '12,63' . self::NBSP . 'Kč'],
            ['input' => '12.638000', 'domainId' => 1, 'result' => '12,638' . self::NBSP . 'Kč'],
            ['input' => 12.630000, 'domainId' => 1, 'result' => '12,63' . self::NBSP . 'Kč'],
            [
                'input' => '123456789.123456789',
                'domainId' => 1,
                'result' => '123' . self::NBSP . '456' . self::NBSP . '789,123456789' . self::NBSP . 'Kč',
            ],
            ['input' => null, 'domainId' => 1, 'result' => null],
            ['input' => 'asdf', 'domainId' => 1, 'result' => 'asdf'],

            ['input' => '12', 'domainId' => 2, 'result' => '€12.00'],
            ['input' => '12.00', 'domainId' => 2, 'result' => '€12.00'],
            ['input' => '12.600', 'domainId' => 2, 'result' => '€12.60'],
            ['input' => '12.630000', 'domainId' => 2, 'result' => '€12.63'],
            ['input' => '12.638000', 'domainId' => 2, 'result' => '€12.638'],
            ['input' => 12.630000, 'domainId' => 2, 'result' => '€12.63'],
            ['input' => '123456789.123456789', 'domainId' => 2, 'result' => '€123,456,789.123456789'],
        ];
    }

    /**
     * @dataProvider priceFilterDataProvider
     */
    public function testPriceFilter($input, $domainId, $result) {
        $currencyFacade = $this->getContainer()->get(CurrencyFacade::class);
        /* @var $currencyFacade \Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyFacade */
        $domain = $this->getContainer()->get(Domain::class);
        /* @var $domain \Shopsys\ShopBundle\Component\Domain\Domain */
        $localization = $this->getContainer()->get(Localization::class);
        /* @var $localization \Shopsys\ShopBundle\Model\Localization\Localization */
        $intlCurrencyRepository = $this->getContainer()->get(IntlCurrencyRepository::class);
        /* @var $intlCurrencyRepository \Shopsys\ShopBundle\Model\Localization\IntlCurrencyRepository */

        $numberFormatRepository = $this->getContainer()->get(NumberFormatRepositoryInterface::class);
        /* @var $numberFormatRepository \CommerceGuys\Intl\NumberFormat\NumberFormatRepositoryInterface */

        $domain->switchDomainById($domainId);

        $priceExtension = new PriceExtension(
            $currencyFacade,
            $domain,
            $localization,
            $numberFormatRepository,
            $intlCurrencyRepository
        );

        $this->assertSame($result, $priceExtension->priceFilter($input));
    }
}
