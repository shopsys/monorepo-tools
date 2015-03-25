<?php

namespace SS6\ShopBundle\Tests\Component\DataFixture;

use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Component\DataFixture\PersistentReference;
use SS6\ShopBundle\Component\DataFixture\PersistentReferenceRepository;
use SS6\ShopBundle\Component\DataFixture\PersistentReferenceService;
use SS6\ShopBundle\Model\Product\Product;
use stdClass;

class PersistentReferenceServiceTest extends PHPUnit_Framework_TestCase {

	public function testPersistReferenceWrongEntity() {
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

		$persistentReferenceService = new PersistentReferenceService($emMock, $persistentReferenceRepositoryMock);
		$this->setExpectedException(\SS6\ShopBundle\Component\DataFixture\Exception\MethodGetIdDoesNotExistException::class);
		$persistentReferenceService->persistReference('referenceName', new stdClass());
	}

	public function testPersistReference() {
		$emMock = $this->getMockBuilder(EntityManager::class)
			->setMethods(['__construct', 'persist', 'flush'])
			->disableOriginalConstructor()
			->getMock();
		$emMock->expects($this->atLeastOnce())->method('persist');
		$emMock->expects($this->atLeastOnce())->method('flush');

		$persistentReferenceRepositoryMock = $this->getMockBuilder(PersistentReferenceRepository::class)
			->setMethods(['__construct'])
			->disableOriginalConstructor()
			->getMock();

		$productMock1 = $this->getMock(Product::class, [], [], '', false);
		$productMock2 = $this->getMock(Product::class, [], [], '', false);

		$persistentReferenceService = new PersistentReferenceService($emMock, $persistentReferenceRepositoryMock);
		$persistentReferenceService->persistReference('referenceName', $productMock1);
		$persistentReferenceService->persistReference('referenceName', $productMock2);
	}

	public function testGetReference() {
		$persistentReference = new PersistentReference('referenceName', 'entityName', 'entityId');
		$expectedObject = new stdClass();

		$emMock = $this->getMockBuilder(EntityManager::class)
			->setMethods(['__construct', 'find'])
			->disableOriginalConstructor()
			->getMock();
		$emMock->expects($this->once())->method('find')->will($this->returnValue($expectedObject));

		$persistentReferenceRepositoryMock = $this->getMockBuilder(PersistentReferenceRepository::class)
			->setMethods(['__construct', 'getByReferenceName'])
			->disableOriginalConstructor()
			->getMock();
		$persistentReferenceRepositoryMock
			->expects($this->once())
			->method('getByReferenceName')
			->will($this->returnValue($persistentReference));

		$persistentReferenceService = new PersistentReferenceService($emMock, $persistentReferenceRepositoryMock);

		$this->assertSame($expectedObject, $persistentReferenceService->getReference('referenceName'));
	}

	public function testGetReferenceNotFound() {
		$persistentReference = new PersistentReference('referenceName', 'entityName', 'entityId');

		$emMock = $this->getMockBuilder(EntityManager::class)
			->setMethods(['__construct', 'find'])
			->disableOriginalConstructor()
			->getMock();
		$emMock->expects($this->once())->method('find')->will($this->returnValue(null));

		$persistentReferenceRepositoryMock = $this->getMockBuilder(PersistentReferenceRepository::class)
			->setMethods(['__construct', 'getByReferenceName'])
			->disableOriginalConstructor()
			->getMock();
		$persistentReferenceRepositoryMock
			->expects($this->once())
			->method('getByReferenceName')
			->will($this->returnValue($persistentReference));

		$persistentReferenceService = new PersistentReferenceService($emMock, $persistentReferenceRepositoryMock);

		$this->setExpectedException(\SS6\ShopBundle\Component\DataFixture\Exception\EntityNotFoundException::class);
		$persistentReferenceService->getReference('referenceName');
	}

}
