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
		$settingValueArray = [new SettingValue('key', 'value')];

		$entityManagerMock = $this->getMockBuilder(EntityManager::class)
			->disableOriginalConstructor()
			->setMethods(['flush'])
			->getMock();
		$entityManagerMock->expects($this->atLeastOnce())->method('flush');

		$settingValueRepositoryMock = $this->getMockBuilder(SettingValueRepository::class)
			->disableOriginalConstructor()
			->setMethods(['findAllForAllDomains', 'findAllDefault'])
			->getMock();
		$settingValueRepositoryMock->expects($this->atLeastOnce())->method('findAllDefault')->willReturn($settingValueArray);
		$settingValueRepositoryMock->expects($this->atLeastOnce())->method('findAllForAllDomains')->willReturn($settingValueArray);

		$setting = new Setting($entityManagerMock, $settingValueRepositoryMock);
		$this->assertEquals('value', $setting->get('key'));
		$setting->set('key', 'newValue');
		$this->assertEquals('newValue', $setting->get('key'));

		$this->setExpectedException(SettingValueNotFoundException::class);
		$setting->set('key2', 'value');
	}

	public function testSetNotFoundException() {
		$settingValueArray = [new SettingValue('key', 'value')];

		$entityManagerMock = $this->getMockBuilder(EntityManager::class)
			->disableOriginalConstructor()
			->setMethods(['flush'])
			->getMock();
		$entityManagerMock->expects($this->never())->method('flush');

		$settingValueRepositoryMock = $this->getMockBuilder(SettingValueRepository::class)
			->disableOriginalConstructor()
			->setMethods(['findAllForAllDomains', 'findAllDefault'])
			->getMock();
		$settingValueRepositoryMock->expects($this->atLeastOnce())->method('findAllDefault')->willReturn($settingValueArray);
		$settingValueRepositoryMock->expects($this->atLeastOnce())->method('findAllForAllDomains')->willReturn($settingValueArray);

		$setting = new Setting($entityManagerMock, $settingValueRepositoryMock);

		$this->setExpectedException(SettingValueNotFoundException::class);
		$setting->set('key2', 'value');
	}

	public function testSetInvalidArgumentException() {
		$entityManagerMock = $this->getMockBuilder(EntityManager::class)
			->disableOriginalConstructor()
			->setMethods(['flush'])
			->getMock();
		$entityManagerMock->expects($this->never())->method('flush');

		$settingValueRepositoryMock = $this->getMockBuilder(SettingValueRepository::class)
			->disableOriginalConstructor()
			->getMock();

		$setting = new Setting($entityManagerMock, $settingValueRepositoryMock);

		$this->setExpectedException(InvalidArgumentException::class);
		$setting->set('key2', 'value', null);
	}

	public function testGetNotFoundException() {
		$settingValueArray = [new SettingValue('key', 'value')];

		$entityManagerMock = $this->getMockBuilder(EntityManager::class)
			->disableOriginalConstructor()
			->getMock();
		$entityManagerMock->expects($this->never())->method('flush');

		$settingValueRepositoryMock = $this->getMockBuilder(SettingValueRepository::class)
			->disableOriginalConstructor()
			->setMethods(['findAllForAllDomains', 'findAllDefault'])
			->getMock();
		$settingValueRepositoryMock->expects($this->atLeastOnce())->method('findAllDefault')->willReturn($settingValueArray);
		$settingValueRepositoryMock->expects($this->atLeastOnce())->method('findAllForAllDomains')->willReturn($settingValueArray);

		$setting = new Setting($entityManagerMock, $settingValueRepositoryMock);

		$this->setExpectedException(SettingValueNotFoundException::class);
		$setting->get('key2');
	}

	public function testGetValues() {
		$settingValueArrayDefault = [new SettingValue('key', 'value', null)];
		$settingValueArrayForAllDomains = [new SettingValue('key', 'value', 0)];
		$settingValueArrayByDomainIdMap = [
			[1, [new SettingValue('key', 'value', 1)]],
			[2, []]
		];

		$entityManagerMock = $this->getMockBuilder(EntityManager::class)
			->disableOriginalConstructor()
			->setMethods(['flush'])
			->getMock();
		$entityManagerMock->expects($this->atLeastOnce())->method('flush');

		$settingValueRepositoryMock = $this->getMockBuilder(SettingValueRepository::class)
			->disableOriginalConstructor()
			->setMethods(['findAllForAllDomains', 'findAllDefault', 'findAllByDomainId'])
			->getMock();
		$settingValueRepositoryMock->expects($this->atLeastOnce())
				->method('findAllDefault')->willReturn($settingValueArrayDefault);
		$settingValueRepositoryMock->expects($this->atLeastOnce())
				->method('findAllForAllDomains')->willReturn($settingValueArrayForAllDomains);
		$settingValueRepositoryMock->expects($this->atLeastOnce())
				->method('findAllByDomainId')->willReturnMap($settingValueArrayByDomainIdMap);

		$setting = new Setting($entityManagerMock, $settingValueRepositoryMock);
		$this->assertEquals('value', $setting->get('key'));
		$this->assertEquals('value', $setting->get('key'), 1);
		$setting->set('key', 'newValue', 1);
		$this->assertEquals('newValue', $setting->get('key', 1));
		$setting->set('key', 'newValueDefault');
		$this->assertEquals('newValue', $setting->get('key', 1));
		$this->assertEquals('newValueDefault', $setting->get('key'));
		$this->assertEquals('newValueDefault', $setting->get('key', 2));
	}
}
