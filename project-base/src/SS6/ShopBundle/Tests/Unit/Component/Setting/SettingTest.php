<?php

namespace SS6\ShopBundle\Tests\Unit\Component\Setting;

use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Component\Setting\Exception\InvalidArgumentException;
use SS6\ShopBundle\Component\Setting\Exception\SettingValueNotFoundException;
use SS6\ShopBundle\Component\Setting\Setting;
use SS6\ShopBundle\Component\Setting\SettingValue;
use SS6\ShopBundle\Component\Setting\SettingValueRepository;

class SettingTest extends PHPUnit_Framework_TestCase {

	public function testSet() {
		$settingValueArray = [
			[SettingValue::DOMAIN_ID_COMMON, []],
			[1, [new SettingValue('key', 'value', 1)]],
		];

		$entityManagerMock = $this->getMockBuilder(EntityManager::class)
			->disableOriginalConstructor()
			->setMethods(['flush', 'persist'])
			->getMock();
		$entityManagerMock->expects($this->atLeastOnce())->method('flush');
		$entityManagerMock->expects($this->never())->method('persist');

		$settingValueRepositoryMock = $this->getMockBuilder(SettingValueRepository::class)
			->disableOriginalConstructor()
			->setMethods(['getAllDefault', 'getAllByDomainId'])
			->getMock();
		$settingValueRepositoryMock->expects($this->atLeastOnce())->method('getAllDefault')->willReturn([]);
		$settingValueRepositoryMock->expects($this->atLeastOnce())->method('getAllByDomainId')->willReturnMap($settingValueArray);

		$setting = new Setting($entityManagerMock, $settingValueRepositoryMock);
		$this->assertSame('value', $setting->get('key', 1));
		$setting->set('key', 'newValue', 1);
		$this->assertSame('newValue', $setting->get('key', 1));

		$this->setExpectedException(SettingValueNotFoundException::class);
		$setting->set('key2', 'value', 1);
	}

	public function testSetNotFoundException() {
		$settingValueArray = [
			[SettingValue::DOMAIN_ID_COMMON, []],
			[1, [new SettingValue('key', 'value', 1)]],
		];

		$entityManagerMock = $this->getMockBuilder(EntityManager::class)
			->disableOriginalConstructor()
			->setMethods(['flush', 'persist'])
			->getMock();
		$entityManagerMock->expects($this->never())->method('flush');
		$entityManagerMock->expects($this->never())->method('persist');

		$settingValueRepositoryMock = $this->getMockBuilder(SettingValueRepository::class)
			->disableOriginalConstructor()
			->setMethods(['getAllDefault', 'getAllByDomainId'])
			->getMock();
		$settingValueRepositoryMock->expects($this->atLeastOnce())->method('getAllDefault')->willReturn([]);
		$settingValueRepositoryMock->expects($this->atLeastOnce())->method('getAllByDomainId')->willReturnMap($settingValueArray);

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
			->setMethods(['getAllDefault', 'getAllByDomainId'])
			->getMock();
		$settingValueRepositoryMock->expects($this->atLeastOnce())->method('getAllDefault')->willReturn([]);
		$settingValueRepositoryMock->expects($this->atLeastOnce())->method('getAllByDomainId')->willReturn($settingValueArray);

		$setting = new Setting($entityManagerMock, $settingValueRepositoryMock);

		$this->setExpectedException(SettingValueNotFoundException::class);
		$setting->get('key2', 1);
	}

	public function testGetValues() {
		$settingValueArrayDefault = [new SettingValue('key', 'valueDefault', SettingValue::DOMAIN_ID_DEFAULT)];
		$settingValueArrayByDomainIdMap = [
			[SettingValue::DOMAIN_ID_COMMON, [new SettingValue('key', 'valueCommon', SettingValue::DOMAIN_ID_COMMON)]],
			[1, [new SettingValue('key', 'value', 1)]],
			[2, []],
		];

		$entityManagerMock = $this->getMockBuilder(EntityManager::class)
			->disableOriginalConstructor()
			->setMethods(['flush', 'persist'])
			->getMock();
		$entityManagerMock->expects($this->atLeastOnce())->method('flush');
		$entityManagerMock->expects($this->never())->method('persist');

		$settingValueRepositoryMock = $this->getMockBuilder(SettingValueRepository::class)
			->disableOriginalConstructor()
			->setMethods(['getAllDefault', 'getAllByDomainId'])
			->getMock();
		$settingValueRepositoryMock->expects($this->atLeastOnce())
				->method('getAllDefault')->willReturn($settingValueArrayDefault);
		$settingValueRepositoryMock->expects($this->atLeastOnce())
				->method('getAllByDomainId')->willReturnMap($settingValueArrayByDomainIdMap);

		$setting = new Setting($entityManagerMock, $settingValueRepositoryMock);
		$this->assertSame('valueCommon', $setting->get('key', SettingValue::DOMAIN_ID_COMMON));
		$this->assertSame('value', $setting->get('key', 1));
		$setting->set('key', 'newValue', 1);
		$this->assertSame('newValue', $setting->get('key', 1));
		$setting->set('key', 'newValueCommon', SettingValue::DOMAIN_ID_COMMON);
		$this->assertSame('newValue', $setting->get('key', 1));
		$this->assertSame('newValueCommon', $setting->get('key', SettingValue::DOMAIN_ID_COMMON));
		$this->assertSame('newValueCommon', $setting->get('key', 2));
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
			->setMethods(['getAllDefault', 'getAllByDomainId'])
			->getMock();
		$settingValueRepositoryMock->expects($this->atLeastOnce())
				->method('getAllDefault')->willReturn($settingValueArrayDefault);
		$settingValueRepositoryMock->expects($this->atLeastOnce())
				->method('getAllByDomainId')->willReturnMap($settingValueArrayByDomainIdMap);

		$setting = new Setting($entityManagerMock, $settingValueRepositoryMock);
		$this->assertSame('valueCommon', $setting->get('key', 2));
		$setting->set('key', 'newValue', 2);
		$this->assertSame('value', $setting->get('key', 1));
		$this->assertSame('newValue', $setting->get('key', 2));
		$this->assertSame('valueCommon', $setting->get('key', 3));
	}

}
