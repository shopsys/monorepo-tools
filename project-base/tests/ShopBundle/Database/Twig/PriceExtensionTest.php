<?php

namespace Tests\ShopBundle\Database\Twig;

use CommerceGuys\Intl\NumberFormat\NumberFormatRepositoryInterface;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Localization\IntlCurrencyRepository;
use Shopsys\FrameworkBundle\Model\Localization\Localization;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade;
use Shopsys\FrameworkBundle\Twig\PriceExtension;
use Tests\ShopBundle\Test\FunctionalTestCase;

class PriceExtensionTest extends FunctionalTestCase
{
    const NBSP = "\xc2\xa0";

    public function priceFilterDataProvider()
    {
        return [
            ['input' => '12', 'domainId' => 1, 'result' => 'CZK12.00'],
            ['input' => '12.00', 'domainId' => 1, 'result' => 'CZK12.00'],
            ['input' => '12.600', 'domainId' => 1, 'result' => 'CZK12.60'],
            ['input' => '12.630000', 'domainId' => 1, 'result' => 'CZK12.63'],
            ['input' => '12.638000', 'domainId' => 1, 'result' => 'CZK12.638'],
            ['input' => 12.630000, 'domainId' => 1, 'result' => 'CZK12.63'],
            [
                'input' => '123456789.123456789',
                'domainId' => 1,
                'result' => 'CZK123,456,789.12346',
            ],
            ['input' => null, 'domainId' => 1, 'result' => null],
            ['input' => 'asdf', 'domainId' => 1, 'result' => 'asdf'],

            ['input' => '12', 'domainId' => 2, 'result' => '12,00' . self::NBSP . '€'],
            ['input' => '12.00', 'domainId' => 2, 'result' => '12,00' . self::NBSP . '€'],
            ['input' => '12.600', 'domainId' => 2, 'result' => '12,60' . self::NBSP . '€'],
            ['input' => '12.630000', 'domainId' => 2, 'result' => '12,63' . self::NBSP . '€'],
            ['input' => '12.638000', 'domainId' => 2, 'result' => '12,638' . self::NBSP . '€'],
            ['input' => 12.630000, 'domainId' => 2, 'result' => '12,63' . self::NBSP . '€'],
            [
                'input' => '123456789.123456789',
                'domainId' => 2,
                'result' => '123' . self::NBSP . '456' . self::NBSP . '789,12346' . self::NBSP . '€',
            ],
        ];
    }

    /**
     * @dataProvider priceFilterDataProvider
     */
    public function testPriceFilter($input, $domainId, $result)
    {
        $currencyFacade = $this->getServiceByType(CurrencyFacade::class);
        /* @var $currencyFacade \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade */
        $domain = $this->getServiceByType(Domain::class);
        /* @var $domain \Shopsys\FrameworkBundle\Component\Domain\Domain */
        $localization = $this->getServiceByType(Localization::class);
        /* @var $localization \Shopsys\FrameworkBundle\Model\Localization\Localization */
        $intlCurrencyRepository = $this->getServiceByType(IntlCurrencyRepository::class);
        /* @var $intlCurrencyRepository \Shopsys\FrameworkBundle\Model\Localization\IntlCurrencyRepository */

        $numberFormatRepository = $this->getServiceByType(NumberFormatRepositoryInterface::class);
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
