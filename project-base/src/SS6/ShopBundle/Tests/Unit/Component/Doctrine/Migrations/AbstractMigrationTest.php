<?php

namespace SS6\ShopBundle\Tests\Unit\Component\Doctrine;

use PHPUnit_Framework_TestCase;
use ReflectionClass;
use SS6\ShopBundle\Component\Doctrine\Migrations\AbstractMigration;

class AbstractMigrationTest extends PHPUnit_Framework_TestCase {

	public function testAddSqlException() {
		$abstractMigrationMock = $this->getMockBuilder(AbstractMigration::class)
			->setMethods(['addSql'])
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$reflectionClass = new ReflectionClass(AbstractMigration::class);
		$addSqlMethod = $reflectionClass->getMethod('addSql');
		$addSqlMethod->setAccessible(true);

		$this->setExpectedException(\SS6\ShopBundle\Component\Doctrine\Migrations\Exception\MethodIsNotAllowedException::class);

		$addSqlMethod->invokeArgs($abstractMigrationMock, ['']);
	}

}
