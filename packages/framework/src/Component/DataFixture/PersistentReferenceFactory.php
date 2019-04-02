<?php

namespace Shopsys\FrameworkBundle\Component\DataFixture;

use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;

class PersistentReferenceFactory implements PersistentReferenceFactoryInterface
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
     * @param string $referenceName
     * @param string $entityName
     * @param int $entityId
     * @return \Shopsys\FrameworkBundle\Component\DataFixture\PersistentReference
     */
    public function create(
        string $referenceName,
        string $entityName,
        int $entityId
    ): PersistentReference {
        $classData = $this->entityNameResolver->resolve(PersistentReference::class);

        return new $classData($referenceName, $entityName, $entityId);
    }
}
