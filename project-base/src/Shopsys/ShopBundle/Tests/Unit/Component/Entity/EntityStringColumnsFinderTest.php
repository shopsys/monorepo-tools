<?php

namespace Shopsys\ShopBundle\Tests\Unit\Component\Entity;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Component\Entity\EntityStringColumnsFinder;

class EntityStringColumnsFinderTest extends PHPUnit_Framework_TestCase {

    public function testGetAllStringColumnNamesIndexedByTableName() {
        $classMetadataInfoMock = $this->getMock(ClassMetadataInfo::class, [], [], '', false);
        $classMetadataInfoMock
            ->method('getTableName')
            ->willReturn('EntityName');
        $classMetadataInfoMock
            ->method('getFieldNames')
            ->willReturn(['stringField', 'textField', 'otherField']);
        $classMetadataInfoMock
            ->method('getTypeOfField')
            ->willReturnCallback(function ($fieldName) {
                if ($fieldName === 'stringField') {
                    return 'string';
                } elseif ($fieldName === 'textField') {
                    return 'text';
                }
                return 'other';
            });
        $classMetadataInfoMock
            ->method('getColumnName')
            ->willReturnCallback(function ($fieldName) {
                if ($fieldName === 'stringField') {
                    return 'string_field';
                } elseif ($fieldName === 'textField') {
                    return 'text_field';
                }
            });

        $expectedResult = [
            'EntityName' => [
                'string_field',
                'text_field',
            ],
        ];

        $entityStringColumnsFinder = new EntityStringColumnsFinder();
        $actualResult = $entityStringColumnsFinder->getAllStringColumnNamesIndexedByTableName([$classMetadataInfoMock]);

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testGetAllStringColumnNamesIndexedByTableNameException() {
        $classMetadataMock = $this->getMock(ClassMetadata::class);
        $this->setExpectedException(\Shopsys\ShopBundle\Component\Entity\Exception\UnexpectedTypeException::class);

        $entityNotNullableColumnsFinder = new EntityStringColumnsFinder();
        $entityNotNullableColumnsFinder->getAllStringColumnNamesIndexedByTableName([$classMetadataMock]);
    }

}
