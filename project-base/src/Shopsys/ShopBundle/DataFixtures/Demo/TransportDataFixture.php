<?php

namespace Shopsys\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\DataFixtures\Base\CurrencyDataFixture;
use Shopsys\ShopBundle\DataFixtures\Base\VatDataFixture;
use Shopsys\ShopBundle\Model\Transport\TransportEditData;
use Shopsys\ShopBundle\Model\Transport\TransportFacade;

class TransportDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    const TRANSPORT_CZECH_POST = 'transport_cp';
    const TRANSPORT_PPL = 'transport_ppl';
    const TRANSPORT_PERSONAL = 'transport_personal';

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $transportEditData = new TransportEditData();
        $transportEditData->transportData->name = [
            'cs' => 'Česká pošta - balík do ruky',
        ];
        $transportEditData->prices = [
            $this->getReference(CurrencyDataFixture::CURRENCY_CZK)->getId() => 99.95,
            $this->getReference(CurrencyDataFixture::CURRENCY_EUR)->getId() => 3.95,
        ];
        $transportEditData->transportData->description = [
            'cs' => 'Pouze na vlastní nebezpečí',
        ];
        $transportEditData->transportData->instructions = [
            'cs' => '<b>Pozor!</b> Česká pošta pouze na vlastní nebezpečí.',
        ];
        $transportEditData->transportData->vat = $this->getReference(VatDataFixture::VAT_HIGH);
        $transportEditData->transportData->domains = [Domain::FIRST_DOMAIN_ID];
        $transportEditData->transportData->hidden = false;
        $this->createTransport(self::TRANSPORT_CZECH_POST, $transportEditData);

        $transportEditData->transportData->name = [
            'cs' => 'PPL',
        ];
        $transportEditData->prices = [
            $this->getReference(CurrencyDataFixture::CURRENCY_CZK)->getId() => 199.95,
            $this->getReference(CurrencyDataFixture::CURRENCY_EUR)->getId() => 6.95,
        ];
        $transportEditData->transportData->description = [
            'cs' => null,
        ];
        $transportEditData->transportData->instructions = [];
        $this->createTransport(self::TRANSPORT_PPL, $transportEditData);

        $transportEditData->transportData->name = [
            'cs' => 'Osobní převzetí',
        ];
        $transportEditData->prices = [
            $this->getReference(CurrencyDataFixture::CURRENCY_CZK)->getId() => 0,
            $this->getReference(CurrencyDataFixture::CURRENCY_EUR)->getId() => 0,
        ];
        $transportEditData->transportData->description = [
            'cs' => 'Uvítá Vás milý personál!',
        ];
        $transportEditData->transportData->vat = $this->getReference(VatDataFixture::VAT_ZERO);
        $this->createTransport(self::TRANSPORT_PERSONAL, $transportEditData);
    }

    /**
     * @param string $referenceName
     * @param \Shopsys\ShopBundle\Model\Transport\TransportEditData $transportEditData
     */
    private function createTransport($referenceName, TransportEditData $transportEditData)
    {
        $transportFacade = $this->get(TransportFacade::class);
        /* @var $transportFacade \Shopsys\ShopBundle\Model\Transport\TransportFacade */

        $transport = $transportFacade->create($transportEditData);
        $this->addReference($referenceName, $transport);
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return [
            VatDataFixture::class,
            CurrencyDataFixture::class,
        ];
    }
}
