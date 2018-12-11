<?php

namespace Shopsys\FrameworkBundle\Component\Image;

use Shopsys\FrameworkBundle\Component\FileUpload\FileUpload;
use Shopsys\FrameworkBundle\Component\Image\Config\ImageEntityConfig;
use Shopsys\FrameworkBundle\Component\Image\Processing\ImageProcessor;

class ImageService
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\Processing\ImageProcessor
     */
    private $imageProcessor;

    /**
     * @var \Shopsys\FrameworkBundle\Component\FileUpload\FileUpload
     */
    private $fileUpload;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\ImageFactoryInterface
     */
    protected $imageFactory;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Image\Processing\ImageProcessor $imageProcessor
     * @param \Shopsys\FrameworkBundle\Component\FileUpload\FileUpload $fileUpload
     * @param \Shopsys\FrameworkBundle\Component\Image\ImageFactoryInterface $imageFactory
     */
    public function __construct(
        ImageProcessor $imageProcessor,
        FileUpload $fileUpload,
        ImageFactoryInterface $imageFactory
    ) {
        $this->imageProcessor = $imageProcessor;
        $this->fileUpload = $fileUpload;
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

        $image = $this->imageFactory->create(
            $imageEntityConfig->getEntityName(),
            $entityId,
            $type,
            $this->imageProcessor->convertToShopFormatAndGetNewFilename($temporaryFilepath)
        );

        return $image;
    }
}
