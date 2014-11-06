<?php

namespace SS6\ShopBundle\Model\Grid\Ordering;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Grid\Ordering\GridOrderingService;
use SS6\ShopBundle\Model\Grid\Ordering\OrderingEntityInterface;

class GridOrderingServiceTest extends PHPUnit_Framework_TestCase {

	public function testGetEntityNameNotModified() {
		$gridOrderingService = new GridOrderingService();
		$entityClass = 'Some\Class' . GridOrderingService::ENTITY_NAME_PREFIX . 'Entity';

		$this->assertEquals($entityClass, $gridOrderingService->getEntityName($entityClass));
	}

	public function testGetEntityName() {
		$gridOrderingService = new GridOrderingService();
		$entityClass = GridOrderingService::ENTITY_NAME_PREFIX . 'Some\Class';

		$this->assertNotEquals($entityClass, $gridOrderingService->getEntityName($entityClass));
	}

	public function testGetEntityClassNotModified() {
		$gridOrderingService = new GridOrderingService();
		$entityName = 'Entity' . GridOrderingService::ENTITY_NAME_PREFIX_REPLACED . 'Some\Class';

		$this->assertEquals($entityName, $gridOrderingService->getEntityClass($entityName));
	}

	public function testGetEntityClass() {
		$gridOrderingService = new GridOrderingService();
		$entityName = GridOrderingService::ENTITY_NAME_PREFIX_REPLACED . 'Some\Class';

		$this->assertNotEquals($entityName, $gridOrderingService->getEntityClass($entityName));
	}

	public function testEntityNameToEntityClassAndBack() {
		$gridOrderingService = new GridOrderingService();
		$entityClass = GridOrderingService::ENTITY_NAME_PREFIX . 'Some\Class';
		$entityName = $gridOrderingService->getEntityName($entityClass);

		$this->assertEquals($entityClass, $gridOrderingService->getEntityClass($entityName));
	}

	public function testSetPositionNull() {
		$gridOrderingService = new GridOrderingService();
		$entity = null;
		
		$this->setExpectedException(\SS6\ShopBundle\Model\Grid\Ordering\Exception\OrderingEntityNotSupportException::class);
		$gridOrderingService->setPosition($entity, 0);
	}

	public function testSetPositionWrongEntity() {
		$gridOrderingService = new GridOrderingService();
		$entity = new \StdClass();
		
		$this->setExpectedException(\SS6\ShopBundle\Model\Grid\Ordering\Exception\OrderingEntityNotSupportException::class);
		$gridOrderingService->setPosition($entity, 0);
	}

	public function testSetPosition() {
		$gridOrderingService = new GridOrderingService();
		$position = 1;
		$entityMock = $this->getMockBuilder(OrderingEntityInterface::class)
			->setMethods(['setPosition'])
			->getMockForAbstractClass();
		$entityMock->expects($this->once())->method('setPosition')->with($this->equalTo($position));

		$gridOrderingService->setPosition($entityMock, $position);
	}

}
