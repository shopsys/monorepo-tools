<?php

namespace Shopsys\FrameworkBundle\Component\UploadedFile;

use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;
use Shopsys\FrameworkBundle\Component\FileUpload\FileUpload;

class UploadedFileFactory implements UploadedFileFactoryInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\FileUpload\FileUpload
     */
    protected $fileUpload;

    /**
     * @var \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver
     */
    protected $entityNameResolver;

    /**
     * @param \Shopsys\FrameworkBundle\Component\FileUpload\FileUpload $fileUpload
     * @param \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver $entityNameResolver
     */
    public function __construct(
        FileUpload $fileUpload,
        EntityNameResolver $entityNameResolver
    ) {
        $this->fileUpload = $fileUpload;
        $this->entityNameResolver = $entityNameResolver;
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

        $classData = $this->entityNameResolver->resolve(UploadedFile::class);

        return new $classData($entityName, $entityId, pathinfo($temporaryFilepath, PATHINFO_BASENAME));
    }
}
