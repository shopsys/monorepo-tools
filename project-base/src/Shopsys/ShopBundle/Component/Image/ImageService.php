<?php

namespace Shopsys\ShopBundle\Component\Image;

use Shopsys\ShopBundle\Component\FileUpload\FileUpload;
use Shopsys\ShopBundle\Component\Image\Config\ImageEntityConfig;
use Shopsys\ShopBundle\Component\Image\Image;
use Shopsys\ShopBundle\Component\Image\Processing\ImageProcessingService;

class ImageService {

    /**
     * @var \Shopsys\ShopBundle\Component\Image\Processing\ImageProcessingService
     */
    private $imageProcessingService;

    /**
     * @var \Shopsys\ShopBundle\Component\FileUpload\FileUpload
     */
    private $fileUpload;

    /**
     * @param \Shopsys\ShopBundle\Component\Image\Processing\ImageProcessingService $imageProcessingService
     */
    public function __construct(ImageProcessingService $imageProcessingService, FileUpload $fileUpload) {
        $this->imageProcessingService = $imageProcessingService;
        $this->fileUpload = $fileUpload;
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Image\Config\ImageEntityConfig $imageEntityConfig
     * @param int $entityId
     * @param array $temporaryFilenames
     * @param string|null $type
     * @return
     */
    public function getUploadedImages(ImageEntityConfig $imageEntityConfig, $entityId, array $temporaryFilenames, $type) {
        if (!$imageEntityConfig->isMultiple($type)) {
            $message = 'Entity ' . $imageEntityConfig->getEntityClass()
                . ' is not allowed to have multiple images for type ' . ($type ?: 'NULL');
            throw new \Shopsys\ShopBundle\Component\Image\Exception\EntityMultipleImageException($message);
        }

        $images = [];
        foreach ($temporaryFilenames as $temporaryFilename) {
            $images[] = $this->createImage($imageEntityConfig, $entityId, $temporaryFilename, $type, null);
        }

        return $images;
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Image\Config\ImageEntityConfig $imageEntityConfig
     * @param int $entityId
     * @param string $temporaryFilename
     * @param string|null $type
     * @return \Shopsys\ShopBundle\Component\Image\Image
     */
    public function createImage(
        ImageEntityConfig $imageEntityConfig,
        $entityId,
        $temporaryFilename,
        $type
    ) {
        $temporaryFilepath = $this->fileUpload->getTemporaryFilePath($temporaryFilename);

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
     * @param \Shopsys\ShopBundle\Component\Image\Image[] $images
     */
    public function deleteImages($entityName, $entityId, array $images) {
        foreach ($images as $image) {
            $this->deleteImage($entityName, $entityId, $image);
        }
    }

    /**
     * @param string $entityName
     * @param int $entityId
     * @param \Shopsys\ShopBundle\Component\Image\Image $image
     */
    private function deleteImage($entityName, $entityId, Image $image) {
        if ($image->getEntityName() !== $entityName
            || $image->getEntityId() !== $entityId
        ) {
            throw new \Shopsys\ShopBundle\Component\Image\Exception\ImageNotFoundException(
                sprintf(
                    'Entity %s with ID %s does not own image with ID',
                    $entityName,
                    $entityId,
                    $image->getId()
                )
            );
        }
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Image\Image[] $imagesByPosition
     */
    public function setImagePositions($imagesByPosition) {
        foreach ($imagesByPosition as $position => $image) {
            $image->setPosition($position);
        }
    }

}
