<?php

namespace Shopsys\FrameworkBundle\Component\UploadedFile;

use Shopsys\FrameworkBundle\Component\FileUpload\FileUpload;

class UploadedFileFactory implements UploadedFileFactoryInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\FileUpload\FileUpload
     */
    protected $fileUpload;

    /**
     * @param \Shopsys\FrameworkBundle\Component\FileUpload\FileUpload $fileUpload
     */
    public function __construct(FileUpload $fileUpload)
    {
        $this->fileUpload = $fileUpload;
    }

    /**
     * @param string $entityName
     * @param int $entityId
     * @param array $temporaryFilenames
     * @return \Shopsys\FrameworkBundle\Component\UploadedFile\UploadedFile
     */
    public function create(
        string $entityName,
        int $entityId,
        array $temporaryFilenames
    ): UploadedFile {
        $temporaryFilepath = $this->fileUpload->getTemporaryFilepath(array_pop($temporaryFilenames));

        return new UploadedFile($entityName, $entityId, pathinfo($temporaryFilepath, PATHINFO_BASENAME));
    }
}
