<?php

namespace Shopsys\ShopBundle\Component\Domain;

use Shopsys\ShopBundle\Component\Image\Processing\ImageProcessingService;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\Filesystem\Filesystem;

class DomainService
{

    const DOMAIN_ICON_WIDTH = 48;
    const DOMAIN_ICON_HEIGHT = 33;
    const DOMAIN_ICON_CROP = false;

    /**
     * @var \Shopsys\ShopBundle\Component\Image\Processing\ImageProcessingService
     */
    private $imageProcessingService;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * @var \Symfony\Bridge\Monolog\Logger
     */
    private $logger;

    public function __construct(
        Logger $logger,
        ImageProcessingService $imageProcessingService,
        Filesystem $filesystem
    ) {
        $this->logger = $logger;
        $this->imageProcessingService = $imageProcessingService;
        $this->filesystem = $filesystem;
    }

    /**
     * @param int $domainId
     * @param string $filepath
     * @param string $domainImagesDirectory
     */
    public function convertToDomainIconFormatAndSave($domainId, $filepath, $domainImagesDirectory) {
        $newTemporaryFilepath = pathinfo($filepath, PATHINFO_DIRNAME)
            . '/'
            . $domainId
            . '.'
            . ImageProcessingService::EXTENSION_PNG;

        $resizedImage = $this->imageProcessingService->resize(
            $this->imageProcessingService->createInterventionImage($filepath),
            self::DOMAIN_ICON_WIDTH,
            self::DOMAIN_ICON_HEIGHT,
            self::DOMAIN_ICON_CROP
        );
        $resizedImage->save($newTemporaryFilepath);

        $targetFileName = pathinfo($newTemporaryFilepath, PATHINFO_BASENAME);
        $targetFilePath = $domainImagesDirectory . '/' . $targetFileName;

        try {
            $this->filesystem->rename($newTemporaryFilepath, $targetFilePath, true);
            $this->filesystem->remove($filepath);
        } catch (\Symfony\Component\Filesystem\Exception\IOException $ex) {
            $message = 'Move file from temporary directory to domain directory failed';
            $moveToFolderFailedException = new \Shopsys\ShopBundle\Component\FileUpload\Exception\MoveToFolderFailedException(
                $message,
                $ex
            );
            $this->logger->addError($message, ['exception' => $moveToFolderFailedException]);
            throw $moveToFolderFailedException;
        }
    }
}
