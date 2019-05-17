<?php

namespace Tests\FrameworkBundle\Unit\Model\Pricing\Vat;

use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;
use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatFacade;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatFactory;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatRepository;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;
use stdClass;

class VatFacadeTest extends TestCase
{
    public function testGetDefaultVat()
    {
        $expected = new stdClass();
        $emMock = $this->createMock(EntityManager::class);

        $settingMock = $this->getMockBuilder(Setting::class)
            ->setMethods(['get', '__construct'])
            ->disableOriginalConstructor()
            ->getMock();
        $settingMock
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo(Vat::SETTING_DEFAULT_VAT))
            ->willReturn(1);

        $vatRepositoryMock = $this->getMockBuilder(VatRepository::class)
            ->setMethods(['findById', '__construct'])
            ->disableOriginalConstructor()
            ->getMock();
        $vatRepositoryMock
            ->expects($this->once())
            ->method('findById')
            ->with($this->equalTo(1))
            ->willReturn($expected);

        $productPriceRecalculationSchedulerMock = $this->getMockBuilder(ProductPriceRecalculationScheduler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $vatFacade = new VatFacade(
            $emMock,
            $vatRepositoryMock,
            $settingMock,
            $productPriceRecalculationSchedulerMock,
            new VatFactory(new EntityNameResolver([]))
        );

        /** @var \stdClass $defaultVat */
        $defaultVat = $vatFacade->getDefaultVat();

        $this->assertSame($expected, $defaultVat);
    }

    public function testSetDefaultVat()
    {
        $emMock = $this->createMock(EntityManager::class);
        $vatRepositoryMock = $this->createMock(VatRepository::class);

        $vatMock = $this->getMockBuilder(Vat::class)
            ->setMethods(['getId', '__construct'])
            ->disableOriginalConstructor()
            ->getMock();
        $vatMock->expects($this->once())->method('getId')->willReturn(1);

        $settingMock = $this->getMockBuilder(Setting::class)
            ->setMethods(['set', '__construct'])
            ->disableOriginalConstructor()
            ->getMock();
        $settingMock
            ->expects($this->once())
            ->method('set')
            ->with($this->equalTo(Vat::SETTING_DEFAULT_VAT), $this->equalTo(1));

        $productPriceRecalculationSchedulerMock = $this->getMockBuilder(ProductPriceRecalculationScheduler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $vatFacade = new VatFacade(
            $emMock,
            $vatRepositoryMock,
            $settingMock,
            $productPriceRecalculationSchedulerMock,
            new VatFactory(new EntityNameResolver([]))
        );
        $vatFacade->setDefaultVat($vatMock);
    }
}
