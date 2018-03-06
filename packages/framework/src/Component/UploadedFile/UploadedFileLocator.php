<?php

namespace Shopsys\FrameworkBundle\Component\UploadedFile;

use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;

class UploadedFileLocator
{
    /**
     * @var string
     */
    private $uploadedFileDir;

    /**
     * @var string
     */
    private $uploadedFileUrlPrefix;

    /**
     * @param string $uploadedFileDir
     * @param string $uploadedFileUrlPrefix
     */
    public function __construct($uploadedFileDir, $uploadedFileUrlPrefix)
    {
        $this->uploadedFileDir = $uploadedFileDir;
        $this->uploadedFileUrlPrefix = $uploadedFileUrlPrefix;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\UploadedFile\UploadedFile $uploadedFile
     * @return string
     */
    public function getRelativeUploadedFileFilepath(UploadedFile $uploadedFile)
    {
        return $this->getRelativeFilePath($uploadedFile->getEntityName()) . '/' . $uploadedFile->getFilename();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\UploadedFile\UploadedFile $uploadedFile
     * @return string
     */
    public function getAbsoluteUploadedFileFilepath(UploadedFile $uploadedFile)
    {
        return $this->getAbsoluteFilePath($uploadedFile->getEntityName()) . '/' . $uploadedFile->getFilename();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param \Shopsys\FrameworkBundle\Component\UploadedFile\UploadedFile $uploadedFile
     * @return string
     */
    public function getUploadedFileUrl(DomainConfig $domainConfig, UploadedFile $uploadedFile)
    {
        if ($this->fileExists($uploadedFile)) {
            return $domainConfig->getUrl()
            . $this->uploadedFileUrlPrefix
            . $this->getRelativeUploadedFileFilepath($uploadedFile);
        }

        throw new \Shopsys\FrameworkBundle\Component\UploadedFile\Exception\FileNotFoundException();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\UploadedFile\UploadedFile $uploadedFile
     * @return bool
     */
    public function fileExists(UploadedFile $uploadedFile)
    {
        $fileFilepath = $this->getAbsoluteUploadedFileFilepath($uploadedFile);

        return is_file($fileFilepath) && is_readable($fileFilepath);
    }

    /**
     * @param string $entityName
     * @return string
     */
    private function getRelativeFilePath($entityName)
    {
        return $entityName;
    }

    /**
     * @param string $entityName
     * @return string
     */
    public function getAbsoluteFilePath($entityName)
    {
        return $this->uploadedFileDir . $this->getRelativeFilePath($entityName);
    }
}
