<?php

namespace SS6\ShopBundle\Tests\Setting;

use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Setting\Exception\SettingValueNotFoundException;
use SS6\ShopBundle\Model\Setting\Exception\InvalidArgumentException;
use SS6\ShopBundle\Model\Setting\Setting;
use SS6\ShopBundle\Model\Setting\SettingValue;
use SS6\ShopBundle\Model\Setting\SettingValueRepository;

class SettingTest extends PHPUnit_Framework_TestCase {

	public function testSet() {
		$settingValueArray = [
			[SettingValue::DOMAIN_ID_COMMON, []],
			[1, [new SettingValue('key', 'value', 1)]]
		];

		$entityManagerMock = $this->getMockBuilder(EntityManager::class)
			->disableOriginalConstructor()
			->setMethods(['flush', 'persist'])
			->getMock();
		$entityManagerMock->expects($this->atLeastOnce())->method('flush');
		$entityManagerMock->expects($this->never())->method('persist');

		$settingValueRepositoryMock = $this->getMockBuilder(SettingValueRepository::class)
			->disableOriginalConstructor()
			->setMethods(['findAllDefault', 'findAllByDomainId'])
			->getMock();
		$settingValueRepositoryMock->expects($this->atLeastOnce())->method('findAllDefault')->willReturn([]);
		$settingValueRepositoryMock->expects($this->atLeastOnce())->method('findAllByDomainId')->willReturnMap($settingValueArray);

		$setting = new Setting($entityManagerMock, $settingValueRepositoryMock);
		$this->assertEquals('value', $setting->get('key', 1));
		$setting->set('key', 'newValue', 1);
		$this->assertEquals('newValue', $setting->get('key', 1));

		$this->setExpectedException(SettingValueNotFoundException::class);
		$setting->set('key2', 'value', 1);
	}

	public function testSetNotFoundException() {
		$settingValueArray = [
			[SettingValue::DOMAIN_ID_COMMON, []],
			[1, [new SettingValue('key', 'value', 1)]]
		];

		$entityManagerMock = $this->getMockBuilder(EntityManager::class)
			->disableOriginalConstructor()
			->setMethods(['flush', 'persist'])
			->getMock();
		$entityManagerMock->expects($this->never())->method('flush');
		$entityManagerMock->expects($this->never())->method('persist');

		$settingValueRepositoryMock = $this->getMockBuilder(SettingValueRepository::class)
			->disableOriginalConstructor()
			->setMethods(['findAllDefault', 'findAllByDomainId'])
			->getMock();
		$settingValueRepositoryMock->expects($this->atLeastOnce())->method('findAllDefault')->willReturn([]);
		$settingValueRepositoryMock->expects($this->atLeastOnce())->method('findAllByDomainId')->willReturnMap($settingValueArray);

		$setting = new Setting($entityManagerMock, $settingValueRepositoryMock);

		$this->setExpectedException(SettingValueNotFoundException::class);
		$setting->set('key2', 'value', 1);
	}

	public function testSetInvalidArgumentException() {
		$entityManagerMock = $this->getMockBuilder(EntityManager::class)
			->disableOriginalConstructor()
			->setMethods(['flush', 'persist'])
			->getMock();
		$entityManagerMock->expects($this->never())->method('flush');
		$entityManagerMock->expects($this->never())->method('persist');

		$settingValueRepositoryMock = $this->getMockBuilder(SettingValueRepository::class)
			->disableOriginalConstructor()
			->getMock();

		$setting = new Setting($entityManagerMock, $settingValueRepositoryMock);

		$this->setExpectedException(InvalidArgumentException::class);
		$setting->set('key2', 'value', null);
	}

	public function testGetNotFoundException() {
		$settingValueArray = [new SettingValue('key', 'value', 1)];

		$entityManagerMock = $this->getMockBuilder(EntityManager::class)
			->disableOriginalConstructor()
			->setMethods(['flush', 'persist'])
			->getMock();
		$entityManagerMock->expects($this->never())->method('flush');
		$entityManagerMock->expects($this->never())->method('persist');

		$settingValueRepositoryMock = $this->getMockBuilder(SettingValueRepository::class)
			->disableOriginalConstructor()
			->setMethods(['findAllDefault', 'findAllByDomainId'])
			->getMock();
		$settingValueRepositoryMock->expects($this->atLeastOnce())->method('findAllDefault')->willReturn([]);
		$settingValueRepositoryMock->expects($this->atLeastOnce())->method('findAllByDomainId')->willReturn($settingValueArray);

		$setting = new Setting($entityManagerMock, $settingValueRepositoryMock);

		$this->setExpectedException(SettingValueNotFoundException::class);
		$setting->get('key2', 1);
	}

	public function testGetValues() {
		$settingValueArrayDefault = [new SettingValue('key', 'valueDefault', SettingValue::DOMAIN_ID_DEFAULT)];
		$settingValueArrayByDomainIdMap = [
			[SettingValue::DOMAIN_ID_COMMON, [new SettingValue('key', 'valueCommon', SettingValue::DOMAIN_ID_COMMON)]],
			[1, [new SettingValue('key', 'value', 1)]],
			[2, []]
		];

		$entityManagerMock = $this->getMockBuilder(EntityManager::class)
			->disableOriginalConstructor()
			->setMethods(['flush', 'persist'])
			->getMock();
		$entityManagerMock->expects($this->atLeastOnce())->method('flush');
		$entityManagerMock->expects($this->never())->method('persist');

		$settingValueRepositoryMock = $this->getMockBuilder(SettingValueRepository::class)
			->disableOriginalConstructor()
			->setMethods(['findAllDefault', 'findAllByDomainId'])
			->getMock();
		$settingValueRepositoryMock->expects($this->atLeastOnce())
				->method('findAllDefault')->willReturn($settingValueArrayDefault);
		$settingValueRepositoryMock->expects($this->atLeastOnce())
				->method('findAllByDomainId')->willReturnMap($settingValueArrayByDomainIdMap);

		$setting = new Setting($entityManagerMock, $settingValueRepositoryMock);
		$this->assertEquals('valueCommon', $setting->get('key', SettingValue::DOMAIN_ID_COMMON));
		$this->assertEquals('value', $setting->get('key', 1));
		$setting->set('key', 'newValue', 1);
		$this->assertEquals('newValue', $setting->get('key', 1));
		$setting->set('key', 'newValueCommon', SettingValue::DOMAIN_ID_COMMON);
		$this->assertEquals('newValue', $setting->get('key', 1));
		$this->assertEquals('newValueCommon', $setting->get('key', SettingValue::DOMAIN_ID_COMMON));
		$this->assertEquals('newValueCommon', $setting->get('key', 2));
	}

	public function testSetValueNewDomain() {
		$settingValueArrayDefault = [new SettingValue('key', 'valueDefault', SettingValue::DOMAIN_ID_DEFAULT)];
		$settingValueArrayByDomainIdMap = [
			[SettingValue::DOMAIN_ID_COMMON, [new SettingValue('key', 'valueCommon', SettingValue::DOMAIN_ID_COMMON)]],
			[1, [new SettingValue('key', 'value', 1)]],
			[2, []],
			[3, []],
		];

		$entityManagerMock = $this->getMockBuilder(EntityManager::class)
			->disableOriginalConstructor()
			->setMethods(['flush', 'persist'])
			->getMock();
		$entityManagerMock->expects($this->atLeastOnce())->method('flush');
		$entityManagerMock->expects($this->atLeastOnce())->method('persist');

		$settingValueRepositoryMock = $this->getMockBuilder(SettingValueRepository::class)
			->disableOriginalConstructor()
			->setMethods(['findAllDefault', 'findAllByDomainId'])
			->getMock();
		$settingValueRepositoryMock->expects($this->atLeastOnce())
				->method('findAllDefault')->willReturn($settingValueArrayDefault);
		$settingValueRepositoryMock->expects($this->atLeastOnce())
				->method('findAllByDomainId')->willReturnMap($settingValueArrayByDomainIdMap);

		$setting = new Setting($entityManagerMock, $settingValueRepositoryMock);
		$this->assertEquals('valueCommon', $setting->get('key', 2));
		$setting->set('key', 'newValue', 2);
		$this->assertEquals('value', $setting->get('key', 1));
		$this->assertEquals('newValue', $setting->get('key', 2));
		$this->assertEquals('valueCommon', $setting->get('key', 3));
	}

}
