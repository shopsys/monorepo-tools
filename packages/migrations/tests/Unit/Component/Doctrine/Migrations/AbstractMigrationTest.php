<?php

namespace Shopsys\MigrationBundle\Tests\Unit\Component\Doctrine\Migrations;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class AbstractMigrationTest extends TestCase
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

        $this->expectException(\Shopsys\MigrationBundle\Component\Doctrine\Migrations\Exception\MethodIsNotAllowedException::class);

        $addSqlMethod->invokeArgs($abstractMigrationMock, ['']);
    }
}
