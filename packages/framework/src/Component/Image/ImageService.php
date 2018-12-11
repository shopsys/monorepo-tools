<?php

namespace Shopsys\FrameworkBundle\Component\Image;

use Shopsys\FrameworkBundle\Component\Image\Config\ImageEntityConfig;

class ImageService
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\ImageFactoryInterface
     */
    protected $imageFactory;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Image\ImageFactoryInterface $imageFactory
     */
    public function __construct(ImageFactoryInterface $imageFactory)
    {
        $this->imageFactory = $imageFactory;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Image\Config\ImageEntityConfig $imageEntityConfig
     * @param int $entityId
     * @param array $temporaryFilenames
     * @param string|null $type
     * @return \Shopsys\FrameworkBundle\Component\Image\Image[]
     */
    public function getUploadedImages(ImageEntityConfig $imageEntityConfig, $entityId, array $temporaryFilenames, $type)
    {
        if (!$imageEntityConfig->isMultiple($type)) {
            $message = 'Entity ' . $imageEntityConfig->getEntityClass()
                . ' is not allowed to have multiple images for type ' . ($type ?: 'NULL');
            throw new \Shopsys\FrameworkBundle\Component\Image\Exception\EntityMultipleImageException($message);
        }

        $images = [];
        foreach ($temporaryFilenames as $temporaryFilename) {
            $images[] = $this->imageFactory->create($imageEntityConfig->getEntityName(), $entityId, $type, $temporaryFilename);
        }

        return $images;
    }
}
