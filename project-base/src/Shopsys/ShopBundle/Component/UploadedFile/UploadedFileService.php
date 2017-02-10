<?php

namespace Shopsys\ShopBundle\Component\UploadedFile;

use Shopsys\ShopBundle\Component\FileUpload\FileUpload;
use Shopsys\ShopBundle\Component\UploadedFile\Config\UploadedFileEntityConfig;
use Shopsys\ShopBundle\Component\UploadedFile\UploadedFile;

class UploadedFileService
{
    /**
     * @var \Shopsys\ShopBundle\Component\FileUpload\FileUpload
     */
    private $fileUpload;

    public function __construct(FileUpload $fileUpload)
    {
        $this->fileUpload = $fileUpload;
    }

    /**
     * @param \Shopsys\ShopBundle\Component\UploadedFile\Config\UploadedFileEntityConfig $uploadedFileEntityConfig
     * @param int $entityId
     * @param string[] $temporaryFilenames
     * @return \Shopsys\ShopBundle\Component\UploadedFile\UploadedFile
     */
    public function createUploadedFile(
        UploadedFileEntityConfig $uploadedFileEntityConfig,
        $entityId,
        array $temporaryFilenames
    ) {
        $temporaryFilepath = $this->fileUpload->getTemporaryFilePath(array_pop($temporaryFilenames));

        return new UploadedFile(
            $uploadedFileEntityConfig->getEntityName(),
            $entityId,
            pathinfo($temporaryFilepath, PATHINFO_BASENAME)
        );
    }
}
