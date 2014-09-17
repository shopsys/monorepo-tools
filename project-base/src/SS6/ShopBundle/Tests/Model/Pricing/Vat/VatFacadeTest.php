<?php

namespace SS6\ShopBundle\Tests\Model\Pricing\Vat;

use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatFacade;
use SS6\ShopBundle\Model\Pricing\Vat\VatRepository;
use SS6\ShopBundle\Model\Pricing\Vat\VatService;
use SS6\ShopBundle\Model\Setting\Setting;
use stdClass;

class VatFacadeTest extends PHPUnit_Framework_TestCase {

	public function testFindDefaultVat() {
		$expected = new stdClass();
		$emMock = $this->getMock(EntityManager::class, [], [], '', false);
		$vatServiceMock = $this->getMock(VatService::class, [], [], '', false);

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

		$vatFacade = new VatFacade($emMock, $vatRepositoryMock, $vatServiceMock, $settingMock);

		$this->assertEquals($expected, $vatFacade->findDefaultVat());
	}

	public function testSetDefaultVat() {
		$emMock = $this->getMock(EntityManager::class, [], [], '', false);
		$vatServiceMock = $this->getMock(VatService::class, [], [], '', false);
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

		$vatFacade = new VatFacade($emMock, $vatRepositoryMock, $vatServiceMock, $settingMock);
		$vatFacade->setDefaultVat($vatMock);
	}

}
