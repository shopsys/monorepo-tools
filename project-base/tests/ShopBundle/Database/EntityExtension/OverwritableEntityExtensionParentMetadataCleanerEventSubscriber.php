<?php

namespace Tests\ShopBundle\Database\EntityExtension;

use Shopsys\FrameworkBundle\Component\EntityExtension\EntityExtensionParentMetadataCleanerEventSubscriber;

class OverwritableEntityExtensionParentMetadataCleanerEventSubscriber extends EntityExtensionParentMetadataCleanerEventSubscriber
{
    /**
     * @param string[] $entityExtensionMap
     */
    public function overwriteEntityExtensionMap(array $entityExtensionMap): void
    {
        $this->entityExtensionParentNames = array_keys($entityExtensionMap);
    }
}
