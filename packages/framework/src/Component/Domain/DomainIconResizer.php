<?php

namespace Shopsys\FrameworkBundle\Component\Domain;

use League\Flysystem\FilesystemInterface;
use Shopsys\FrameworkBundle\Component\Image\Processing\ImageProcessor;
use Symfony\Bridge\Monolog\Logger;

class DomainIconResizer
{
    /** @access protected */
    const DOMAIN_ICON_WIDTH = 48;
    /** @access protected */
    const DOMAIN_ICON_HEIGHT = 33;
    /** @access protected */
    const DOMAIN_ICON_CROP = false;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\Processing\ImageProcessor
     */
    protected $imageProcessor;

    /**
     * @var \Symfony\Bridge\Monolog\Logger
     */
    protected $logger;

    /**
     * @var \League\Flysystem\FilesystemInterface
     */
    protected $filesystem;

    /**
     * @param \Symfony\Bridge\Monolog\Logger $logger
     * @param \Shopsys\FrameworkBundle\Component\Image\Processing\ImageProcessor $imageProcessor
     * @param \League\Flysystem\FilesystemInterface $filesystem
     */
    public function __construct(
        Logger $logger,
        ImageProcessor $imageProcessor,
        FilesystemInterface $filesystem
    ) {
        $this->logger = $logger;
        $this->imageProcessor = $imageProcessor;
        $this->filesystem = $filesystem;
    }

    /**
     * @param int $domainId
     * @param string $filepath
     * @param string $domainImagesDirectory
     */
    public function convertToDomainIconFormatAndSave($domainId, $filepath, $domainImagesDirectory)
    {
        $resizedImage = $this->imageProcessor->resize(
            $this->imageProcessor->createInterventionImage($filepath),
            static::DOMAIN_ICON_WIDTH,
            static::DOMAIN_ICON_HEIGHT,
            static::DOMAIN_ICON_CROP
        );
        $resizedImage->encode(ImageProcessor::EXTENSION_PNG);

        $targetFilePath = $domainImagesDirectory . '/' . $domainId . '.' . ImageProcessor::EXTENSION_PNG;

        try {
            $this->filesystem->put($targetFilePath, $resizedImage);
        } catch (\Exception $ex) {
            $message = 'Move file from temporary directory to domain directory failed';
            $moveToFolderFailedException = new \Shopsys\FrameworkBundle\Component\FileUpload\Exception\MoveToFolderFailedException(
                $message,
                $ex
            );
            $this->logger->addError($message, ['exception' => $moveToFolderFailedException]);
            throw $moveToFolderFailedException;
        }
    }
}
