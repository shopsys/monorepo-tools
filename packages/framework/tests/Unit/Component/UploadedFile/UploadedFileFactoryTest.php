<?php

namespace Tests\FrameworkBundle\Unit\Component\UploadedFile;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;
use Shopsys\FrameworkBundle\Component\FileUpload\FileUpload;
use Shopsys\FrameworkBundle\Component\UploadedFile\UploadedFileFactory;

class UploadedFileFactoryTest extends TestCase
{
    public function testCreate()
    {
        $temporaryFilename = 'temporaryFilename.tmp';
        $temporaryFilenames = [$temporaryFilename];
        $temporaryFilepath = 'path/' . $temporaryFilename;
        $entityId = 1;
        $entityName = 'entityName';

        $fileUploadMock = $this->getMockBuilder(FileUpload::class)
            ->setMethods(['getTemporaryFilePath'])
            ->disableOriginalConstructor()
            ->getMock();
        $fileUploadMock
            ->expects($this->once())
            ->method('getTemporaryFilePath')
            ->with($this->equalTo($temporaryFilename))
            ->willReturn($temporaryFilepath);

        $uploadedFileFactory = new UploadedFileFactory($fileUploadMock, new EntityNameResolver([]));
        $uploadedFile = $uploadedFileFactory->create($entityName, $entityId, $temporaryFilenames);
        $filesForUpload = $uploadedFile->getTemporaryFilesForUpload();
        $fileForUpload = array_pop($filesForUpload);
        /* @var $fileForUpload \Shopsys\FrameworkBundle\Component\FileUpload\FileForUpload */

        $this->assertSame($entityId, $uploadedFile->getEntityId());
        $this->assertSame($entityName, $uploadedFile->getEntityName());
        $this->assertSame($temporaryFilename, $fileForUpload->getTemporaryFilename());
        $this->assertFalse($fileForUpload->isImage());
    }
}
