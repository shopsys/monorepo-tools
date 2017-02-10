<?php

namespace Shopsys\ShopBundle\Tests\Unit\Component\Setting;

use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Component\Setting\Exception\InvalidArgumentException;
use Shopsys\ShopBundle\Component\Setting\Exception\SettingValueNotFoundException;
use Shopsys\ShopBundle\Component\Setting\Setting;
use Shopsys\ShopBundle\Component\Setting\SettingValue;
use Shopsys\ShopBundle\Component\Setting\SettingValueRepository;

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
            ->setMethods(['getAllByDomainId'])
            ->getMock();
        $settingValueRepositoryMock->expects($this->atLeastOnce())->method('getAllByDomainId')->willReturnMap($settingValueArray);

        $setting = new Setting($entityManagerMock, $settingValueRepositoryMock);
        $this->assertSame('value', $setting->getForDomain('key', 1));
        $setting->setForDomain('key', 'newValue', 1);
        $this->assertSame('newValue', $setting->getForDomain('key', 1));

        $this->setExpectedException(SettingValueNotFoundException::class);
        $setting->setForDomain('key2', 'value', 1);
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
            ->setMethods(['getAllByDomainId'])
            ->getMock();
        $settingValueRepositoryMock->expects($this->atLeastOnce())->method('getAllByDomainId')->willReturnMap($settingValueArray);

        $setting = new Setting($entityManagerMock, $settingValueRepositoryMock);

        $this->setExpectedException(SettingValueNotFoundException::class);
        $setting->setForDomain('key2', 'value', 1);
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
        $setting->setForDomain('key2', 'value', null);
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
            ->setMethods(['getAllByDomainId'])
            ->getMock();
        $settingValueRepositoryMock->expects($this->atLeastOnce())->method('getAllByDomainId')->willReturn($settingValueArray);

        $setting = new Setting($entityManagerMock, $settingValueRepositoryMock);

        $this->setExpectedException(SettingValueNotFoundException::class);
        $setting->getForDomain('key2', 1);
    }

    public function testGetValues() {
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
            ->setMethods(['getAllByDomainId'])
            ->getMock();
        $settingValueRepositoryMock->expects($this->atLeastOnce())
                ->method('getAllByDomainId')->willReturnMap($settingValueArrayByDomainIdMap);

        $setting = new Setting($entityManagerMock, $settingValueRepositoryMock);
        $this->assertSame('valueCommon', $setting->get('key'));
        $this->assertSame('value', $setting->getForDomain('key', 1));
        $setting->setForDomain('key', 'newValue', 1);
        $this->assertSame('newValue', $setting->getForDomain('key', 1));
        $setting->set('key', 'newValueCommon');
        $this->assertSame('newValue', $setting->getForDomain('key', 1));
        $this->assertSame('newValueCommon', $setting->get('key'));
    }

    public function testSetValueNewDomain() {
        $settingValueArrayByDomainIdMap = [
            [SettingValue::DOMAIN_ID_COMMON, [new SettingValue('key', 'valueCommon', SettingValue::DOMAIN_ID_COMMON)]],
            [1, [new SettingValue('key', 'value', 1)]],
            [2, []],
            [3, []],
        ];

        $entityManagerMock = $this->createDummyEntityManagerMock();

        $settingValueRepositoryMock = $this->getMockBuilder(SettingValueRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAllByDomainId'])
            ->getMock();
        $settingValueRepositoryMock->expects($this->atLeastOnce())
                ->method('getAllByDomainId')->willReturnMap($settingValueArrayByDomainIdMap);

        $setting = new Setting($entityManagerMock, $settingValueRepositoryMock);

        $this->assertSame('value', $setting->getForDomain('key', 1));
    }

    public function testCannotSetNonexistentCommonValue() {
        $entityManagerMock = $this->createDummyEntityManagerMock();

        $settingValueRepositoryMock = $this->getMock(SettingValueRepository::class, [], [], '', false);
        $settingValueRepositoryMock->expects($this->any())->method('getAllByDomainId')->willReturn([]);

        $setting = new Setting($entityManagerMock, $settingValueRepositoryMock);

        $this->setExpectedException(
            SettingValueNotFoundException::class,
            'Common setting value with name "nonexistentKey" not found.'
        );
        $setting->set('nonexistentKey', 'anyValue');
    }

    public function testCannotSetNonexistentValueForDomain() {
        $entityManagerMock = $this->createDummyEntityManagerMock();

        $settingValueRepositoryMock = $this->getMock(SettingValueRepository::class, [], [], '', false);
        $settingValueRepositoryMock->expects($this->any())->method('getAllByDomainId')->willReturn([]);

        $setting = new Setting($entityManagerMock, $settingValueRepositoryMock);

        $this->setExpectedException(
            SettingValueNotFoundException::class,
            'Setting value with name "nonexistentKey" for domain ID "1" not found.'
        );
        $setting->setForDomain('nonexistentKey', 'anyValue', 1);
    }

    private function createDummyEntityManagerMock() {
        return $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
