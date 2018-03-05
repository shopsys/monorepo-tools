<?php

namespace Tests\ShopBundle\Unit\Component\DataFixture;

use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\DataFixture\PersistentReference;
use Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFacade;
use Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceRepository;
use Shopsys\FrameworkBundle\Model\Product\Product;
use stdClass;

class PersistentReferenceFacadeTest extends TestCase
{
    public function testCannotPersistReferenceToEntityWithoutGetIdMethod()
    {
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
        $this->expectException(\Shopsys\FrameworkBundle\Component\DataFixture\Exception\MethodGetIdDoesNotExistException::class);
        $persistentReferenceFacade->persistReference('referenceName', new stdClass());
    }

    public function testCanPersistNewReference()
    {
        $emMock = $this->getMockBuilder(EntityManager::class)
            ->setMethods(['__construct', 'persist', 'flush'])
            ->disableOriginalConstructor()
            ->getMock();
        $emMock->expects($this->atLeastOnce())->method('persist');
        $emMock->expects($this->atLeastOnce())->method('flush');

        $persistentReferenceRepositoryMock = $this->getMockBuilder(PersistentReferenceRepository::class)
            ->setMethods(['__construct', 'getByReferenceName'])
            ->disableOriginalConstructor()
            ->getMock();

        $expectedException = new \Shopsys\FrameworkBundle\Component\DataFixture\Exception\PersistentReferenceNotFoundException('newReferenceName');
        $persistentReferenceRepositoryMock->method('getByReferenceName')->willThrowException($expectedException);

        $productMock = $this->getMockBuilder(Product::class)
            ->setMethods(['getId'])
            ->disableOriginalConstructor()
            ->getMock();

        $productMock->expects($this->any())->method('getId')->willReturn(1);

        $persistentReferenceFacade = new PersistentReferenceFacade($emMock, $persistentReferenceRepositoryMock);
        $persistentReferenceFacade->persistReference('newReferenceName', $productMock);
    }

    public function testGetReference()
    {
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

    public function testGetReferenceNotFound()
    {
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

        $this->expectException(\Shopsys\FrameworkBundle\Component\DataFixture\Exception\EntityNotFoundException::class);
        $persistentReferenceFacade->getReference('referenceName');
    }
}
