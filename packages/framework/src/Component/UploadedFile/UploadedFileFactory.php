<?php

namespace Shopsys\FrameworkBundle\Component\UploadedFile;

class UploadedFileFactory implements UploadedFileFactoryInterface
{
    /**
     * @param string $entityName
     * @param int $entityId
     * @param string|null $temporaryFilename
     * @return \Shopsys\FrameworkBundle\Component\UploadedFile\UploadedFile
     */
    public function create(
        string $entityName,
        int $entityId,
        ?string $temporaryFilename
    ): UploadedFile {
        return new UploadedFile($entityName, $entityId, $temporaryFilename);
    }
}
