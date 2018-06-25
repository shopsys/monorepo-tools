<?php

namespace Shopsys\FrameworkBundle\Component\Doctrine;

use Doctrine\ORM\Mapping\ClassMetadataInfo;

class EntityStringColumnsFinder
{
    /**
     * @param \Doctrine\ORM\Mapping\ClassMetadata[] $classesMetadata
     * @return string[][]
     */
    public function getAllStringColumnNamesIndexedByTableName(array $classesMetadata)
    {
        $stringColumnNamesIndexedByTableName = [];
        foreach ($classesMetadata as $classMetadata) {
            if (!($classMetadata instanceof ClassMetadataInfo)) {
                $message = 'Instance of ' . ClassMetadataInfo::class . ' is required.';
                throw new \Shopsys\FrameworkBundle\Component\Doctrine\Exception\UnexpectedTypeException($message);
            }
            $stringColumnNames = $this->getStringColumnNames($classMetadata);
            if (!empty($stringColumnNames)) {
                $stringColumnNamesIndexedByTableName[$classMetadata->getTableName()] = $stringColumnNames;
            }
        }

        return $stringColumnNamesIndexedByTableName;
    }

    /**
     * @param \Doctrine\ORM\Mapping\ClassMetadataInfo $classMetadataInfo
     * @return string[]
     */
    private function getStringColumnNames(ClassMetadataInfo $classMetadataInfo)
    {
        $stringColumnNames = [];
        foreach ($classMetadataInfo->getFieldNames() as $fieldName) {
            if (in_array($classMetadataInfo->getTypeOfField($fieldName), $this->getDoctrineStringTypes(), true)) {
                $stringColumnNames[] = $classMetadataInfo->getColumnName($fieldName);
            }
        }

        return $stringColumnNames;
    }

    /**
     * @return string[]
     */
    private function getDoctrineStringTypes()
    {
        return [
            'text',
            'string',
        ];
    }
}
