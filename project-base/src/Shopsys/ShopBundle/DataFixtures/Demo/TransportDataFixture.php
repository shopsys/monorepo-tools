<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\DataFixtures\Base\CurrencyDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Base\VatDataFixture;
use Shopsys\FrameworkBundle\Model\Transport\TransportEditData;
use Shopsys\FrameworkBundle\Model\Transport\TransportFacade;

class TransportDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    const TRANSPORT_CZECH_POST = 'transport_cp';
    const TRANSPORT_PPL = 'transport_ppl';
    const TRANSPORT_PERSONAL = 'transport_personal';

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function load(ObjectManager $manager)
    {
        $transportEditData = new TransportEditData();
        $transportEditData->transportData->name = [
            'cs' => 'Česká pošta - balík do ruky',
            'en' => 'Czech post',
        ];
        $transportEditData->pricesByCurrencyId = [
            $this->getReference(CurrencyDataFixture::CURRENCY_CZK)->getId() => 99.95,
            $this->getReference(CurrencyDataFixture::CURRENCY_EUR)->getId() => 3.95,
        ];
        $transportEditData->transportData->description = [
            'cs' => 'Pouze na vlastní nebezpečí',
            'en' => 'Only if you are crazy',
        ];
        $transportEditData->transportData->instructions = [
            'cs' => '<b>Pozor!</b> Česká pošta pouze na vlastní nebezpečí.',
            'en' => '<b>Warning!</b> Use Czech Post only if you are crazy.',
        ];
        $transportEditData->transportData->vat = $this->getReference(VatDataFixture::VAT_HIGH);
        $transportEditData->transportData->domains = [Domain::FIRST_DOMAIN_ID];
        $transportEditData->transportData->hidden = false;
        $this->createTransport(self::TRANSPORT_CZECH_POST, $transportEditData);

        $transportEditData->transportData->name = [
            'cs' => 'PPL',
            'en' => 'PPL',
        ];
        $transportEditData->pricesByCurrencyId = [
            $this->getReference(CurrencyDataFixture::CURRENCY_CZK)->getId() => 199.95,
            $this->getReference(CurrencyDataFixture::CURRENCY_EUR)->getId() => 6.95,
        ];
        $transportEditData->transportData->description = [
            'cs' => null,
            'en' => null,
        ];
        $transportEditData->transportData->instructions = [];
        $this->createTransport(self::TRANSPORT_PPL, $transportEditData);

        $transportEditData->transportData->name = [
            'cs' => 'Osobní převzetí',
            'en' => 'Personal collection',
        ];
        $transportEditData->pricesByCurrencyId = [
            $this->getReference(CurrencyDataFixture::CURRENCY_CZK)->getId() => 0,
            $this->getReference(CurrencyDataFixture::CURRENCY_EUR)->getId() => 0,
        ];
        $transportEditData->transportData->description = [
            'cs' => 'Uvítá Vás milý personál!',
            'en' => 'You will be welcomed friendly staff!',
        ];
        $transportEditData->transportData->vat = $this->getReference(VatDataFixture::VAT_ZERO);
        $this->createTransport(self::TRANSPORT_PERSONAL, $transportEditData);
    }

    /**
     * @param string $referenceName
     * @param \Shopsys\FrameworkBundle\Model\Transport\TransportEditData $transportEditData
     */
    private function createTransport($referenceName, TransportEditData $transportEditData)
    {
        $transportFacade = $this->get(TransportFacade::class);
        /* @var $transportFacade \Shopsys\FrameworkBundle\Model\Transport\TransportFacade */

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
