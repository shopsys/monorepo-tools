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
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatService;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;
use stdClass;

class VatFacadeTest extends TestCase
{
    public function testGetDefaultVat()
    {
        $expected = new stdClass();
        $emMock = $this->createMock(EntityManager::class);
        $vatService = new VatService();

        $settingMock = $this->getMockBuilder(Setting::class)
            ->setMethods(['get', '__construct'])
            ->disableOriginalConstructor()
            ->getMock();
        $settingMock
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo(Vat::SETTING_DEFAULT_VAT))
            ->will($this->returnValue(1));

        $vatRepositoryMock = $this->getMockBuilder(VatRepository::class)
            ->setMethods(['findById', '__construct'])
            ->disableOriginalConstructor()
            ->getMock();
        $vatRepositoryMock
            ->expects($this->once())
            ->method('findById')
            ->with($this->equalTo(1))
            ->will($this->returnValue($expected));

        $productPriceRecalculationSchedulerMock = $this->getMockBuilder(ProductPriceRecalculationScheduler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $vatFacade = new VatFacade(
            $emMock,
            $vatRepositoryMock,
            $vatService,
            $settingMock,
            $productPriceRecalculationSchedulerMock,
            new VatFactory(new EntityNameResolver([]))
        );

        $this->assertSame($expected, $vatFacade->getDefaultVat());
    }

    public function testSetDefaultVat()
    {
        $emMock = $this->createMock(EntityManager::class);
        $vatService = new VatService();
        $vatRepositoryMock = $this->createMock(VatRepository::class);

        $vatMock = $this->getMockBuilder(Vat::class)
            ->setMethods(['getId', '__construct'])
            ->disableOriginalConstructor()
            ->getMock();
        $vatMock->expects($this->once())->method('getId')->will($this->returnValue(1));

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
            $vatService,
            $settingMock,
            $productPriceRecalculationSchedulerMock,
            new VatFactory(new EntityNameResolver([]))
        );
        $vatFacade->setDefaultVat($vatMock);
    }
}
