<?php

namespace Shopsys\FrameworkBundle\Component\Image;

interface ImageFactoryInterface
{
    /**
     * @param string $entityName
     * @param int $entityId
     * @param string|null $type
     * @param string|null $temporaryFilename
     * @return \Shopsys\FrameworkBundle\Component\Image\Image
     */
    public function create(
        string $entityName,
        int $entityId,
        ?string $type,
        ?string $temporaryFilename
    ): Image;
}
