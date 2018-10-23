<?php

namespace Tests\ShopBundle\Functional\EntityExtension;

use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;

class OverwritableEntityNameResolver extends EntityNameResolver
{
    /**
     * @param string[] $entityExtensionMap
     */
    public function overwriteEntityExtensionMap(array $entityExtensionMap): void
    {
        $this->entityExtensionMap = $entityExtensionMap;
    }
}
