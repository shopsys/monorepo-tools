<?php

namespace Tests\ShopBundle\Functional\Model\Transport;

use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData;
use Shopsys\FrameworkBundle\Model\Transport\IndependentTransportVisibilityCalculation;
use Shopsys\FrameworkBundle\Model\Transport\TransportDataFactoryInterface;
use Shopsys\ShopBundle\Model\Transport\Transport;
use Tests\ShopBundle\Test\TransactionFunctionalTestCase;

class IndependentTransportVisibilityCalculationTest extends TransactionFunctionalTestCase
{
    protected const FIRST_DOMAIN_ID = 1;
    protected const SECOND_DOMAIN_ID = 2;

    public function testIsIndependentlyVisible()
    {
        $em = $this->getEntityManager();
        $vat = $this->getDefaultVat();

        $enabledOnDomains = [
            1 => true,
            2 => false,
        ];

        $transport = $this->getDefaultTransport($vat, $enabledOnDomains, false);

        $em->persist($vat);
        $em->persist($transport);
        $em->flush();

        /** @var \Shopsys\FrameworkBundle\Model\Transport\IndependentTransportVisibilityCalculation $independentTransportVisibilityCalculation */
        $independentTransportVisibilityCalculation =
            $this->getContainer()->get(IndependentTransportVisibilityCalculation::class);

        $this->assertTrue($independentTransportVisibilityCalculation->isIndependentlyVisible($transport, self::FIRST_DOMAIN_ID));
    }

    public function testIsIndependentlyVisibleEmptyName()
    {
        $em = $this->getEntityManager();
        $vat = $this->getDefaultVat();

        $transportData = $this->getTransportDataFactory()->create();
        $transportData->name = [
            'cs' => null,
            'en' => null,
        ];
        $transportData->vat = $vat;
        $transportData->hidden = false;
        $transportData->enabled = [
            self::FIRST_DOMAIN_ID => true,
            self::SECOND_DOMAIN_ID => false,
        ];

        $transport = new Transport($transportData);

        $em->persist($vat);
        $em->persist($transport);
        $em->flush();

        /** @var \Shopsys\FrameworkBundle\Model\Transport\IndependentTransportVisibilityCalculation $independentTransportVisibilityCalculation */
        $independentTransportVisibilityCalculation =
            $this->getContainer()->get(IndependentTransportVisibilityCalculation::class);

        $this->assertFalse($independentTransportVisibilityCalculation->isIndependentlyVisible($transport, self::FIRST_DOMAIN_ID));
    }

    public function testIsIndependentlyVisibleNotOnDomain()
    {
        $em = $this->getEntityManager();
        $vat = $this->getDefaultVat();

        $enabledOnDomains = [
            self::FIRST_DOMAIN_ID => false,
            self::SECOND_DOMAIN_ID => false,
        ];

        $transport = $this->getDefaultTransport($vat, $enabledOnDomains, false);

        $em->persist($vat);
        $em->persist($transport);
        $em->flush();

        /** @var \Shopsys\FrameworkBundle\Model\Transport\IndependentTransportVisibilityCalculation $independentTransportVisibilityCalculation */
        $independentTransportVisibilityCalculation =
            $this->getContainer()->get(IndependentTransportVisibilityCalculation::class);

        $this->assertFalse($independentTransportVisibilityCalculation->isIndependentlyVisible($transport, self::FIRST_DOMAIN_ID));
    }

    public function testIsIndependentlyVisibleHidden()
    {
        $em = $this->getEntityManager();
        $vat = $this->getDefaultVat();

        $enabledOnDomains = [
            self::FIRST_DOMAIN_ID => true,
            self::SECOND_DOMAIN_ID => false,
        ];

        $transport = $this->getDefaultTransport($vat, $enabledOnDomains, true);

        $em->persist($vat);
        $em->persist($transport);
        $em->flush();

        /** @var \Shopsys\FrameworkBundle\Model\Transport\IndependentTransportVisibilityCalculation $independentTransportVisibilityCalculation */
        $independentTransportVisibilityCalculation =
            $this->getContainer()->get(IndependentTransportVisibilityCalculation::class);

        $this->assertFalse($independentTransportVisibilityCalculation->isIndependentlyVisible($transport, self::FIRST_DOMAIN_ID));
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat $vat
     * @param array $enabledForDomains
     * @param bool $hidden
     * @return \Shopsys\ShopBundle\Model\Transport\Transport
     */
    public function getDefaultTransport(Vat $vat, $enabledForDomains, $hidden)
    {
        $transportDataFactory = $this->getTransportDataFactory();

        $transportData = $transportDataFactory->create();
        $transportData->name = [
            'cs' => 'paymentName',
            'en' => 'paymentName',
        ];

        $transportData->vat = $vat;
        $transportData->hidden = $hidden;
        $transportData->enabled = $enabledForDomains;

        return new Transport($transportData);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat
     */
    private function getDefaultVat()
    {
        $vatData = new VatData();
        $vatData->name = 'vat';
        $vatData->percent = '21';
        return new Vat($vatData);
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Transport\TransportDataFactory
     */
    public function getTransportDataFactory()
    {
        return $this->getContainer()->get(TransportDataFactoryInterface::class);
    }
}
