<?php

namespace SS6\ShopBundle\Tests\Model\DataFixture;

use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;
use SS6\Environment;
use SS6\ShopBundle\Model\DataFixture\PersistentReference;
use SS6\ShopBundle\Model\DataFixture\PersistentReferenceService;
use SS6\ShopBundle\Model\DataFixture\PersistentReferenceRepository;
use SS6\ShopBundle\Model\Product\Product;
use stdClass;
use Symfony\Component\HttpKernel\Kernel;

class PersistentReferenceServiceTest extends PHPUnit_Framework_TestCase {

	public function testPersistReferenceProductionEnvironment() {
		$kernelMock = $this->getMockForAbstractClass(Kernel::class, [], '', false, true, true, ['getEnvironment']);
		$kernelMock
			->expects($this->atLeastOnce())
			->method('getEnvironment')
			->will($this->returnValue(Environment::ENVIRONMENT_PRODUCTION));

		$emMock = $this->getMockBuilder(EntityManager::class)
			->setMethods(['__construct', 'persist', 'flush'])
			->disableOriginalConstructor()
			->getMock();
		$emMock->expects($this->never())->method('persist');
		$emMock->expects($this->never())->method('flush');

		$persistentReferenceRepositoryMock = $this->getMock(PersistentReferenceRepository::class, [], [], '', false);

		$persistentReferenceService = new PersistentReferenceService($kernelMock, $emMock, $persistentReferenceRepositoryMock);
		$persistentReferenceService->persistReference('referenceName', new stdClass);
	}

	public function testPersistReferenceWrongEntity() {
		$kernelMock = $this->getMockForAbstractClass(Kernel::class, [], '', false, true, true, ['getEnvironment']);
		$kernelMock->expects($this->atLeastOnce())->method('getEnvironment')->will($this->returnValue(Environment::ENVIRONMENT_TEST));

		$emMock = $this->getMockBuilder(EntityManager::class)
			->setMethods(['__construct', 'persist', 'flush'])
			->disableOriginalConstructor()
			->getMock();
		$emMock->expects($this->never())->method('persist');
		$emMock->expects($this->never())->method('flush');

		$persistentReferenceRepositoryMock = $this->getMockBuilder(PersistentReferenceRepository::class)
			->setMethods(['__construct', 'deleteAll'])
			->disableOriginalConstructor()
			->getMock();
		$persistentReferenceRepositoryMock->expects($this->never())->method('deleteAll');

		$persistentReferenceService = new PersistentReferenceService($kernelMock, $emMock, $persistentReferenceRepositoryMock);
		$this->setExpectedException(\SS6\ShopBundle\Model\DataFixture\Exception\MethodGetIdDoesNotExistException::class);
		$persistentReferenceService->persistReference('referenceName', new stdClass);
	}

	public function testPersistReference() {
		$kernelMock = $this->getMockForAbstractClass(Kernel::class, [], '', false, true, true, ['getEnvironment']);
		$kernelMock->expects($this->atLeastOnce())->method('getEnvironment')->will($this->returnValue(Environment::ENVIRONMENT_TEST));

		$emMock = $this->getMockBuilder(EntityManager::class)
			->setMethods(['__construct', 'persist', 'flush'])
			->disableOriginalConstructor()
			->getMock();
		$emMock->expects($this->atLeastOnce())->method('persist');
		$emMock->expects($this->atLeastOnce())->method('flush');

		$persistentReferenceRepositoryMock = $this->getMockBuilder(PersistentReferenceRepository::class)
			->setMethods(['__construct', 'deleteAll'])
			->disableOriginalConstructor()
			->getMock();
		$persistentReferenceRepositoryMock->expects($this->once())->method('deleteAll');

		$productMock1 = $this->getMock(Product::class, [], [], '', false);
		$productMock2 = $this->getMock(Product::class, [], [], '', false);


		$persistentReferenceService = new PersistentReferenceService($kernelMock, $emMock, $persistentReferenceRepositoryMock);
		$persistentReferenceService->persistReference('referenceName', $productMock1);
		$persistentReferenceService->persistReference('referenceName', $productMock2);
	}

	public function testGetReference() {
		$kernelMock = $this->getMockForAbstractClass(Kernel::class, [], '', false);

		$persistentReference = new PersistentReference('referenceName', 'entityName', 'entityId');
		$expectedObject = new stdClass();

		$emMock = $this->getMockBuilder(EntityManager::class)
			->setMethods(['__construct', 'find'])
			->disableOriginalConstructor()
			->getMock();
		$emMock->expects($this->once())->method('find')->will($this->returnValue($expectedObject));

		$persistentReferenceRepositoryMock = $this->getMockBuilder(PersistentReferenceRepository::class)
			->setMethods(['__construct', 'get'])
			->disableOriginalConstructor()
			->getMock();
		$persistentReferenceRepositoryMock->expects($this->once())->method('get')->will($this->returnValue($persistentReference));

		$persistentReferenceService = new PersistentReferenceService($kernelMock, $emMock, $persistentReferenceRepositoryMock);

		$this->assertEquals($expectedObject, $persistentReferenceService->getReference('referenceName'));
	}

	public function testGetReferenceNotFound() {
		$kernelMock = $this->getMockForAbstractClass(Kernel::class, [], '', false);

		$persistentReference = new PersistentReference('referenceName', 'entityName', 'entityId');

		$emMock = $this->getMockBuilder(EntityManager::class)
			->setMethods(['__construct', 'find'])
			->disableOriginalConstructor()
			->getMock();
		$emMock->expects($this->once())->method('find')->will($this->returnValue(null));

		$persistentReferenceRepositoryMock = $this->getMockBuilder(PersistentReferenceRepository::class)
			->setMethods(['__construct', 'get'])
			->disableOriginalConstructor()
			->getMock();
		$persistentReferenceRepositoryMock->expects($this->once())->method('get')->will($this->returnValue($persistentReference));

		$persistentReferenceService = new PersistentReferenceService($kernelMock, $emMock, $persistentReferenceRepositoryMock);

		$this->setExpectedException(\SS6\ShopBundle\Model\DataFixture\Exception\EntityNotFoundException::class);
		$persistentReferenceService->getReference('referenceName');
	}

}
