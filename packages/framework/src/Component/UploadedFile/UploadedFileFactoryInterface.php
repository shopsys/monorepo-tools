<?php

namespace Shopsys\FrameworkBundle\Component\UploadedFile;

interface UploadedFileFactoryInterface
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
    ): UploadedFile;
}
