<?php

namespace ShopSys\MigrationBundle\Tests\Unit\Component\Doctrine\Migrations;

use PHPUnit_Framework_TestCase;
use ReflectionClass;
use ShopSys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class AbstractMigrationTest extends PHPUnit_Framework_TestCase
{
    public function testAddSqlException()
    {
        $abstractMigrationMock = $this->getMockBuilder(AbstractMigration::class)
            ->setMethods(['addSql'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $reflectionClass = new ReflectionClass(AbstractMigration::class);
        $addSqlMethod = $reflectionClass->getMethod('addSql');
        $addSqlMethod->setAccessible(true);

        $this->setExpectedException(\ShopSys\MigrationBundle\Component\Doctrine\Migrations\Exception\MethodIsNotAllowedException::class);

        $addSqlMethod->invokeArgs($abstractMigrationMock, ['']);
    }
}
