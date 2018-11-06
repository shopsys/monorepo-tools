<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Demo;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade;

class CurrencyDataFixture extends AbstractReferenceFixture
{
    const CURRENCY_CZK = 'currency_czk';
    const CURRENCY_EUR = 'currency_eur';

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade
     */
    private $currencyFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyDataFactoryInterface
     */
    private $currencyDataFactory;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade $currencyFacade
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyDataFactoryInterface $currencyDataFactory
     */
    public function __construct(
        CurrencyFacade $currencyFacade,
        CurrencyDataFactoryInterface $currencyDataFactory
    ) {
        $this->currencyFacade = $currencyFacade;
        $this->currencyDataFactory = $currencyDataFactory;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        /**
         * The "CZK" currency is created in database migration.
         * @see \Shopsys\FrameworkBundle\Migrations\Version20180603135342
         */
        $currencyCzk = $this->currencyFacade->getById(1);
        $this->addReference(self::CURRENCY_CZK, $currencyCzk);

        $currencyData = $this->currencyDataFactory->create();
        $currencyData->name = 'Euro';
        $currencyData->code = Currency::CODE_EUR;
        $currencyData->exchangeRate = 25;
        $currencyEuro = $this->currencyFacade->create($currencyData);
        $this->addReference(self::CURRENCY_EUR, $currencyEuro);
    }
}
