<?php

declare(strict_types=1);

namespace Shopsys\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatFacade;

class VatDataFixture extends AbstractReferenceFixture
{
    public const VAT_ZERO = 'vat_zero';
    public const VAT_SECOND_LOW = 'vat_second_low';
    public const VAT_LOW = 'vat_low';
    public const VAT_HIGH = 'vat_high';

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Vat\VatFacade
     */
    protected $vatFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Vat\VatDataFactoryInterface
     */
    protected $vatDataFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Setting\Setting
     */
    protected $setting;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\VatFacade $vatFacade
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\VatDataFactoryInterface $vatDataFactory
     * @param \Shopsys\FrameworkBundle\Component\Setting\Setting $setting
     */
    public function __construct(
        VatFacade $vatFacade,
        VatDataFactoryInterface $vatDataFactory,
        Setting $setting
    ) {
        $this->vatFacade = $vatFacade;
        $this->vatDataFactory = $vatDataFactory;
        $this->setting = $setting;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {

        /**
         * Vat with zero rate is created in database migration.
         * @see \Shopsys\FrameworkBundle\Migrations\Version20180603135343
         */
        $vatZeroRate = $this->vatFacade->getById(1);
        $this->addReference(self::VAT_ZERO, $vatZeroRate);

        $vatData = $this->vatDataFactory->create();

        $vatData->name = 'Second reduced rate';
        $vatData->percent = '10';
        $this->createVat($vatData, self::VAT_SECOND_LOW);

        $vatData->name = 'Reduced rate';
        $vatData->percent = '15';
        $this->createVat($vatData, self::VAT_LOW);

        $vatData->name = 'Standard rate';
        $vatData->percent = '21';
        $this->createVat($vatData, self::VAT_HIGH);

        $this->setHighVatAsDefault();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData $vatData
     * @param string|null $referenceName
     */
    protected function createVat(VatData $vatData, $referenceName = null)
    {
        $vat = $this->vatFacade->create($vatData);
        if ($referenceName !== null) {
            $this->addReference($referenceName, $vat);
        }
    }

    protected function setHighVatAsDefault()
    {
        $defaultVat = $this->getReference(self::VAT_HIGH);
        /** @var $defaultVat \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat */
        $this->setting->set(Vat::SETTING_DEFAULT_VAT, $defaultVat->getId());
    }
}
