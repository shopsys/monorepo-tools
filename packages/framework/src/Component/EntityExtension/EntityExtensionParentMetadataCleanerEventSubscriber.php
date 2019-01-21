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
     * @var \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver
     */
    protected $entityNameResolver;

    /**
     * @param \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver $entityNameResolver
     */
    public function __construct(EntityNameResolver $entityNameResolver)
    {
        $this->entityNameResolver = $entityNameResolver;
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
        if ($this->mustClean($entityName)) {
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

    /**
     * @param string $entityName
     * @return bool
     */
    protected function mustClean(string $entityName): bool
    {
        return $this->isExtended($entityName) && !$this->isTranslation($entityName);
    }

    /**
     * @param string $entityName
     * @return bool
     */
    protected function isExtended(string $entityName): bool
    {
        return $this->entityNameResolver->resolve($entityName) !== $entityName;
    }

    /**
     * @param string $entityName
     * @return bool
     */
    protected function isTranslation(string $entityName): bool
    {
        return (bool)preg_match('~Translation$~', $entityName);
    }
}
