<?php

namespace Shopsys\ShopBundle\Tests\Unit\Model\Pricing\Vat;

use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Component\Setting\Setting;
use Shopsys\ShopBundle\Model\Pricing\Vat\Vat;
use Shopsys\ShopBundle\Model\Pricing\Vat\VatFacade;
use Shopsys\ShopBundle\Model\Pricing\Vat\VatRepository;
use Shopsys\ShopBundle\Model\Pricing\Vat\VatService;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;
use stdClass;

class VatFacadeTest extends PHPUnit_Framework_TestCase {

	public function testGetDefaultVat() {
		$expected = new stdClass();
		$emMock = $this->getMock(EntityManager::class, [], [], '', false);
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
			$productPriceRecalculationSchedulerMock
		);

		$this->assertSame($expected, $vatFacade->getDefaultVat());
	}

	public function testSetDefaultVat() {
		$emMock = $this->getMock(EntityManager::class, [], [], '', false);
		$vatService = new VatService();
		$vatRepositoryMock = $this->getMock(VatRepository::class, [], [], '', false);

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
			$productPriceRecalculationSchedulerMock
		);
		$vatFacade->setDefaultVat($vatMock);
	}

}
