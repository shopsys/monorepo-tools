<?php

namespace Shopsys\FrameworkBundle\Component\Image;

use Shopsys\FrameworkBundle\Component\FileUpload\FileUpload;
use Shopsys\FrameworkBundle\Component\Image\Config\ImageEntityConfig;
use Shopsys\FrameworkBundle\Component\Image\Processing\ImageProcessingService;

class ImageService
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\Processing\ImageProcessingService
     */
    private $imageProcessingService;

    /**
     * @var \Shopsys\FrameworkBundle\Component\FileUpload\FileUpload
     */
    private $fileUpload;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Image\Processing\ImageProcessingService $imageProcessingService
     * @param \Shopsys\FrameworkBundle\Component\FileUpload\FileUpload $fileUpload
     */
    public function __construct(ImageProcessingService $imageProcessingService, FileUpload $fileUpload)
    {
        $this->imageProcessingService = $imageProcessingService;
        $this->fileUpload = $fileUpload;
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
            $images[] = $this->createImage($imageEntityConfig, $entityId, $temporaryFilename, $type);
        }

        return $images;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Image\Config\ImageEntityConfig $imageEntityConfig
     * @param int $entityId
     * @param string $temporaryFilename
     * @param string|null $type
     * @return \Shopsys\FrameworkBundle\Component\Image\Image
     */
    public function createImage(
        ImageEntityConfig $imageEntityConfig,
        $entityId,
        $temporaryFilename,
        $type
    ) {
        $temporaryFilepath = $this->fileUpload->getTemporaryFilepath($temporaryFilename);

        $image = new Image(
            $imageEntityConfig->getEntityName(),
            $entityId,
            $type,
            $this->imageProcessingService->convertToShopFormatAndGetNewFilename($temporaryFilepath)
        );

        return $image;
    }

    /**
     * @param string $entityName
     * @param int $entityId
     * @param \Shopsys\FrameworkBundle\Component\Image\Image[] $images
     */
    public function deleteImages($entityName, $entityId, array $images)
    {
        foreach ($images as $image) {
            $this->deleteImage($entityName, $entityId, $image);
        }
    }

    /**
     * @param string $entityName
     * @param int $entityId
     * @param \Shopsys\FrameworkBundle\Component\Image\Image $image
     */
    private function deleteImage($entityName, $entityId, Image $image)
    {
        if ($image->getEntityName() !== $entityName
            || $image->getEntityId() !== $entityId
        ) {
            throw new \Shopsys\FrameworkBundle\Component\Image\Exception\ImageNotFoundException(
                sprintf(
                    'Entity %s with ID %s does not own image with ID %s',
                    $entityName,
                    $entityId,
                    $image->getId()
                )
            );
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Image\Image[] $orderedImages
     */
    public function setImagePositionsByOrder($orderedImages)
    {
        $position = 0;
        foreach ($orderedImages as $image) {
            $image->setPosition($position);
            $position++;
        }
    }
}
