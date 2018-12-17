<?php

namespace Shopsys\FrameworkBundle\Component\Image;

use Shopsys\FrameworkBundle\Component\Image\Config\ImageEntityConfig;

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

    /**
     * @param \Shopsys\FrameworkBundle\Component\Image\Config\ImageEntityConfig $imageEntityConfig
     * @param int $entityId
     * @param string|null $type
     * @param array $temporaryFilenames
     * @return \Shopsys\FrameworkBundle\Component\Image\Image[]
     */
    public function createMultiple(
        ImageEntityConfig $imageEntityConfig,
        int $entityId,
        ?string $type,
        array $temporaryFilenames
    ): array;
}
