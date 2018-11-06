<?php

namespace Shopsys\FrameworkBundle\Component\UploadedFile;

use League\Flysystem\FilesystemInterface;
use Shopsys\FrameworkBundle\Component\UploadedFile\Config\UploadedFileConfig;

class DirectoryStructureCreator
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\UploadedFile\Config\UploadedFileConfig
     */
    private $uploadedFileConfig;

    /**
     * @var \Shopsys\FrameworkBundle\Component\UploadedFile\UploadedFileLocator
     */
    private $uploadedFileLocator;

    /**
     * @var \League\Flysystem\FilesystemInterface
     */
    private $filesysytem;

    /**
     * @param \Shopsys\FrameworkBundle\Component\UploadedFile\Config\UploadedFileConfig $uploadedFileConfig
     * @param \Shopsys\FrameworkBundle\Component\UploadedFile\UploadedFileLocator $uploadedFileLocator
     * @param \League\Flysystem\FilesystemInterface $filesystem
     */
    public function __construct(
        UploadedFileConfig $uploadedFileConfig,
        UploadedFileLocator $uploadedFileLocator,
        FilesystemInterface $filesystem
    ) {
        $this->uploadedFileConfig = $uploadedFileConfig;
        $this->uploadedFileLocator = $uploadedFileLocator;
        $this->filesysytem = $filesystem;
    }

    public function makeUploadedFileDirectories()
    {
        $uploadedFileEntityConfigs = $this->uploadedFileConfig->getAllUploadedFileEntityConfigs();
        $directories = [];
        foreach ($uploadedFileEntityConfigs as $uploadedFileEntityConfig) {
            $directories[] = $this->uploadedFileLocator->getAbsoluteFilePath($uploadedFileEntityConfig->getEntityName());
        }

        foreach ($directories as $directory) {
            $this->filesysytem->createDir($directory);
        }
    }
}
