<?php

namespace Shopsys\FrameworkBundle\Component\UploadedFile;

use Shopsys\FrameworkBundle\Component\FileUpload\FileUpload;
use Shopsys\FrameworkBundle\Component\UploadedFile\Config\UploadedFileEntityConfig;

class UploadedFileService
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\FileUpload\FileUpload
     */
    private $fileUpload;

    public function __construct(FileUpload $fileUpload)
    {
        $this->fileUpload = $fileUpload;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\UploadedFile\Config\UploadedFileEntityConfig $uploadedFileEntityConfig
     * @param int $entityId
     * @param string[] $temporaryFilenames
     * @return \Shopsys\FrameworkBundle\Component\UploadedFile\UploadedFile
     */
    public function createUploadedFile(
        UploadedFileEntityConfig $uploadedFileEntityConfig,
        $entityId,
        array $temporaryFilenames
    ) {
        $temporaryFilepath = $this->fileUpload->getTemporaryFilepath(array_pop($temporaryFilenames));

        return new UploadedFile(
            $uploadedFileEntityConfig->getEntityName(),
            $entityId,
            pathinfo($temporaryFilepath, PATHINFO_BASENAME)
        );
    }
}
