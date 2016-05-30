<?php

namespace SS6\ShopBundle\Tests\Unit\Component\DataFixture;

use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Component\DataFixture\PersistentReference;
use SS6\ShopBundle\Component\DataFixture\PersistentReferenceFacade;
use SS6\ShopBundle\Component\DataFixture\PersistentReferenceRepository;
use SS6\ShopBundle\Model\Product\Product;
use stdClass;

/**
 * @UglyTest
 */
class PersistentReferenceFacadeTest extends PHPUnit_Framework_TestCase {

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

		$persistentReferenceFacade = new PersistentReferenceFacade($emMock, $persistentReferenceRepositoryMock);
		$this->setExpectedException(\SS6\ShopBundle\Component\DataFixture\Exception\MethodGetIdDoesNotExistException::class);
		$persistentReferenceFacade->persistReference('referenceName', new stdClass());
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

		$productMock = $this->getMockBuilder(Product::class)
			->setMethods(['getId'])
			->disableOriginalConstructor()
			->getMock();

		$productMock->expects($this->any())->method('getId')->willReturn(1);

		$persistentReferenceFacade = new PersistentReferenceFacade($emMock, $persistentReferenceRepositoryMock);
		$persistentReferenceFacade->persistReference('referenceName', $productMock);
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

		$persistentReferenceFacade = new PersistentReferenceFacade($emMock, $persistentReferenceRepositoryMock);

		$this->assertSame($expectedObject, $persistentReferenceFacade->getReference('referenceName'));
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

		$persistentReferenceFacade = new PersistentReferenceFacade($emMock, $persistentReferenceRepositoryMock);

		$this->setExpectedException(\SS6\ShopBundle\Component\DataFixture\Exception\EntityNotFoundException::class);
		$persistentReferenceFacade->getReference('referenceName');
	}

}
