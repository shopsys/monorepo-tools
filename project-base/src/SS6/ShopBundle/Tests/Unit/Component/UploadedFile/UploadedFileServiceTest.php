<?php

namespace SS6\ShopBundle\Tests\Unit\Component\UploadedFile;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Component\FileUpload\FileForUpload;
use SS6\ShopBundle\Component\FileUpload\FileUpload;
use SS6\ShopBundle\Component\UploadedFile\Config\UploadedFileEntityConfig;
use SS6\ShopBundle\Component\UploadedFile\UploadedFileService;

/**
 * @UglyTest
 */
class UploadedFileServiceTest extends PHPUnit_Framework_TestCase {

	public function testCreateUploadedFile() {
		$temporaryFilename = 'temporaryFilename.tmp';
		$temporaryFilenames = [$temporaryFilename];
		$temporaryFilepath = 'path/' . $temporaryFilename;
		$entityId = 1;
		$entityName = 'entityName';
		$entityClass = 'entityClass';

		$fileUploadMock = $this->getMock(FileUpload::class, ['getTemporaryFilePath'], [], '', false);
		$fileUploadMock
			->expects($this->once())
			->method('getTemporaryFilePath')
			->with($this->equalTo($temporaryFilename))
			->willReturn($temporaryFilepath);

		$uploadedFileEntityConfig = new UploadedFileEntityConfig($entityName, $entityClass);

		$uploadedFileService = new UploadedFileService($fileUploadMock);
		$uploadedFile = $uploadedFileService->createUploadedFile($uploadedFileEntityConfig, $entityId, $temporaryFilenames);
		$filesForUpload = $uploadedFile->getTemporaryFilesForUpload();
		$fileForUpload = array_pop($filesForUpload);
		/* @var $fileForUpload \SS6\ShopBundle\Component\FileUpload\FileForUpload */

		$this->assertSame($entityId, $uploadedFile->getEntityId());
		$this->assertSame($entityName, $uploadedFile->getEntityName());
		$this->assertSame($temporaryFilename, $fileForUpload->getTemporaryFilename());
		$this->assertFalse($fileForUpload->isImage());
	}
}
