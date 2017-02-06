<?php

namespace SS6\ShopBundle\Tests\Unit\Twig;

use Intervention\Image\Image;
use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Component\FileUpload\FileUpload;
use SS6\ShopBundle\Component\Image\Processing\ImageThumbnailFactory;
use SS6\ShopBundle\Twig\FileThumbnail\FileThumbnailExtension;

class FileThumbnailExtensionTest extends PHPUnit_Framework_TestCase {

	public function testGetFileThumbnailInfoByTemporaryFilenameBrokenImage() {
		$temporaryFilename = 'filename.jpg';

		$fileUploadMock = $this->getMock(FileUpload::class, ['getTemporaryFilepath'], [], '', false);
		$fileUploadMock->expects($this->any())->method('getTemporaryFilepath')->willReturn('dir/' . $temporaryFilename);

		$exception = new \SS6\ShopBundle\Component\Image\Processing\Exception\FileIsNotSupportedImageException($temporaryFilename);
		$imageThumbnailFactoryMock = $this->getMock(ImageThumbnailFactory::class, ['getImageThumbnail'], [], '', false);
		$imageThumbnailFactoryMock->expects($this->once())->method('getImageThumbnail')->willThrowException($exception);

		$fileThumbnailExtension = new FileThumbnailExtension($fileUploadMock, $imageThumbnailFactoryMock);
		$fileThumbnailInfo = $fileThumbnailExtension->getFileThumbnailInfoByTemporaryFilename($temporaryFilename);

		$this->assertSame(FileThumbnailExtension::DEFAULT_ICON_TYPE, $fileThumbnailInfo->getIconType());
		$this->assertNull($fileThumbnailInfo->getImageUri());
	}

	public function testGetFileThumbnailInfoByTemporaryFilenameImage() {
		$temporaryFilename = 'filename.jpg';
		$encodedData = 'encodedData';

		$fileUploadMock = $this->getMock(FileUpload::class, ['getTemporaryFilepath'], [], '', false);
		$fileUploadMock->expects($this->any())->method('getTemporaryFilepath')->willReturn('dir/' . $temporaryFilename);

		$imageMock = $this->getMock(Image::class, ['encode']);
		$imageMock->expects($this->once())->method('encode')->willReturnSelf();
		$imageMock->setEncoded($encodedData);

		$imageThumbnailMock = $this->getMock(ImageThumbnailFactory::class, ['getImageThumbnail'], [], '', false);
		$imageThumbnailMock->expects($this->once())->method('getImageThumbnail')->willReturn($imageMock);

		$fileThumbnailExtension = new FileThumbnailExtension($fileUploadMock, $imageThumbnailMock);
		$fileThumbnailInfo = $fileThumbnailExtension->getFileThumbnailInfoByTemporaryFilename($temporaryFilename);

		$this->assertNull($fileThumbnailInfo->getIconType());
		$this->assertSame($encodedData, $fileThumbnailInfo->getImageUri());
	}

	public function testGetFileThumbnailInfoByTemporaryFilenameImageDocument() {
		$temporaryFilename = 'filename.doc';

		$fileUploadMock = $this->getMock(FileUpload::class, ['getTemporaryFilepath'], [], '', false);
		$fileUploadMock->expects($this->any())->method('getTemporaryFilepath')->willReturn('dir/' . $temporaryFilename);

		$exception = new \SS6\ShopBundle\Component\Image\Processing\Exception\FileIsNotSupportedImageException($temporaryFilename);
		$imageThumbnailFactoryMock = $this->getMock(ImageThumbnailFactory::class, ['getImageThumbnail'], [], '', false);
		$imageThumbnailFactoryMock->expects($this->once())->method('getImageThumbnail')->willThrowException($exception);

		$fileThumbnailExtension = new FileThumbnailExtension($fileUploadMock, $imageThumbnailFactoryMock);
		$fileThumbnailInfo = $fileThumbnailExtension->getFileThumbnailInfoByTemporaryFilename($temporaryFilename);

		$this->assertSame('word', $fileThumbnailInfo->getIconType());
		$this->assertNull($fileThumbnailInfo->getImageUri());
	}
}
