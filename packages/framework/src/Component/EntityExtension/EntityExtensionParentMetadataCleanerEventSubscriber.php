<?php

namespace Shopsys\FrameworkBundle\Component\EntityExtension;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class EntityExtensionParentMetadataCleanerEventSubscriber implements EventSubscriber
{
    /**
     * @var string[]
     */
    protected $entityExtensionParentNames;

    /**
     * @param string[] $entityExtensionMap
     */
    public function __construct(array $entityExtensionMap)
    {
        $this->entityExtensionParentNames = array_keys($entityExtensionMap);
    }

    /**
     * @return string[]
     */
    public function getSubscribedEvents(): array
    {
        return [Events::loadClassMetadata];
    }

    /**
     * @param \Doctrine\ORM\Event\LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
    {
        $meta = $eventArgs->getClassMetadata();
        $entityName = $meta->getName();
        if (in_array($entityName, $this->entityExtensionParentNames, true)) {
            $meta->isMappedSuperclass = true;
            $meta->identifier = [];
            $meta->generatorType = ClassMetadataInfo::GENERATOR_TYPE_NONE;
            $meta->fieldMappings = [];
            $meta->fieldNames = [];
            $meta->columnNames = [];
            $meta->associationMappings = [];
            $meta->idGenerator = new AssignedGenerator();
            $meta->embeddedClasses = [];
            $meta->inheritanceType = ClassMetadataInfo::INHERITANCE_TYPE_NONE;
            $meta->discriminatorColumn = null;
            $meta->discriminatorMap = [];
            $meta->discriminatorValue = null;
        }
    }
}
