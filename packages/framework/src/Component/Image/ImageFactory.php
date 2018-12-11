<?php

namespace Shopsys\FrameworkBundle\Component\Image;

use Shopsys\FrameworkBundle\Component\FileUpload\FileUpload;
use Shopsys\FrameworkBundle\Component\Image\Processing\ImageProcessor;

class ImageFactory implements ImageFactoryInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\Processing\ImageProcessor
     */
    protected $imageProcessor;

    /**
     * @var \Shopsys\FrameworkBundle\Component\FileUpload\FileUpload
     */
    protected $fileUpload;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Image\Processing\ImageProcessor $imageProcessor
     * @param \Shopsys\FrameworkBundle\Component\FileUpload\FileUpload $fileUpload
     */
    public function __construct(ImageProcessor $imageProcessor, FileUpload $fileUpload)
    {
        $this->imageProcessor = $imageProcessor;
        $this->fileUpload = $fileUpload;
    }

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
    ): Image {
        $temporaryFilePath = $this->fileUpload->getTemporaryFilepath($temporaryFilename);
        $convertedFilePath = $this->imageProcessor->convertToShopFormatAndGetNewFilename($temporaryFilePath);

        return new Image($entityName, $entityId, $type, $convertedFilePath);
    }
}
