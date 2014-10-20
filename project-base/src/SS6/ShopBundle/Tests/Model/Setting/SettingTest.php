<?php

namespace SS6\ShopBundle\Tests\Setting;

use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Setting\Exception\SettingValueNotFoundException;
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
			->setMethods(['findAll'])
			->getMock();
		$settingValueRepositoryMock->expects($this->atLeastOnce())->method('findAll')->willReturn($settingValueArray);

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
			->setMethods(['findAll'])
			->getMock();
		$settingValueRepositoryMock->expects($this->atLeastOnce())->method('findAll')->willReturn($settingValueArray);

		$setting = new Setting($entityManagerMock, $settingValueRepositoryMock);

		$this->setExpectedException(SettingValueNotFoundException::class);
		$setting->set('key2', 'value');
	}

	public function testGetNotFoundException() {
		$settingValueArray = [new SettingValue('key', 'value')];

		$entityManagerMock = $this->getMockBuilder(EntityManager::class)
			->disableOriginalConstructor()
			->getMock();
		$entityManagerMock->expects($this->never())->method('flush');

		$settingValueRepositoryMock = $this->getMockBuilder(SettingValueRepository::class)
			->disableOriginalConstructor()
			->setMethods(['findAll'])
			->getMock();
		$settingValueRepositoryMock->expects($this->atLeastOnce())->method('findAll')->willReturn($settingValueArray);

		$setting = new Setting($entityManagerMock, $settingValueRepositoryMock);

		$this->setExpectedException(SettingValueNotFoundException::class);
		$setting->get('key2');
	}

}
