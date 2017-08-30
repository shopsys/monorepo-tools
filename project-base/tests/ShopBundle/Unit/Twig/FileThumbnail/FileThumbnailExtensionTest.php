<?php

namespace Tests\ShopBundle\Unit\Twig\FileThumbnail;

use Intervention\Image\Image;
use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Component\FileUpload\FileUpload;
use Shopsys\ShopBundle\Component\Image\Processing\ImageThumbnailFactory;
use Shopsys\ShopBundle\Twig\FileThumbnail\FileThumbnailExtension;

class FileThumbnailExtensionTest extends PHPUnit_Framework_TestCase
{
    public function testGetFileThumbnailInfoByTemporaryFilenameBrokenImage()
    {
        $temporaryFilename = 'filename.jpg';

        $fileUploadMock = $this->getMockBuilder(FileUpload::class)
            ->setMethods(['getTemporaryFilepath'])
            ->disableOriginalConstructor()
            ->getMock();
        $fileUploadMock->expects($this->any())->method('getTemporaryFilepath')->willReturn('dir/' . $temporaryFilename);

        $exception = new \Shopsys\ShopBundle\Component\Image\Processing\Exception\FileIsNotSupportedImageException($temporaryFilename);
        $imageThumbnailFactoryMock = $this->getMockBuilder(ImageThumbnailFactory::class)
            ->setMethods(['getImageThumbnail'])
            ->disableOriginalConstructor()
            ->getMock();
        $imageThumbnailFactoryMock->expects($this->once())->method('getImageThumbnail')->willThrowException($exception);

        $fileThumbnailExtension = new FileThumbnailExtension($fileUploadMock, $imageThumbnailFactoryMock);
        $fileThumbnailInfo = $fileThumbnailExtension->getFileThumbnailInfoByTemporaryFilename($temporaryFilename);

        $this->assertSame(FileThumbnailExtension::DEFAULT_ICON_TYPE, $fileThumbnailInfo->getIconType());
        $this->assertNull($fileThumbnailInfo->getImageUri());
    }

    public function testGetFileThumbnailInfoByTemporaryFilenameImage()
    {
        $temporaryFilename = 'filename.jpg';
        $encodedData = 'encodedData';

        $fileUploadMock = $this->getMockBuilder(FileUpload::class)
            ->setMethods(['getTemporaryFilepath'])
            ->disableOriginalConstructor()
            ->getMock();
        $fileUploadMock->expects($this->any())->method('getTemporaryFilepath')->willReturn('dir/' . $temporaryFilename);

        $imageMock = $this->getMockBuilder(Image::class)
            ->setMethods(['encode'])
            ->disableOriginalConstructor()
            ->getMock();
        $imageMock->expects($this->once())->method('encode')->willReturnSelf();
        $imageMock->setEncoded($encodedData);

        $imageThumbnailMock = $this->getMockBuilder(ImageThumbnailFactory::class)
            ->setMethods(['getImageThumbnail'])
            ->disableOriginalConstructor()
            ->getMock();
        $imageThumbnailMock->expects($this->once())->method('getImageThumbnail')->willReturn($imageMock);

        $fileThumbnailExtension = new FileThumbnailExtension($fileUploadMock, $imageThumbnailMock);
        $fileThumbnailInfo = $fileThumbnailExtension->getFileThumbnailInfoByTemporaryFilename($temporaryFilename);

        $this->assertNull($fileThumbnailInfo->getIconType());
        $this->assertSame($encodedData, $fileThumbnailInfo->getImageUri());
    }

    public function testGetFileThumbnailInfoByTemporaryFilenameImageDocument()
    {
        $temporaryFilename = 'filename.doc';

        $fileUploadMock = $this->getMockBuilder(FileUpload::class)
            ->setMethods(['getTemporaryFilepath'])
            ->disableOriginalConstructor()
            ->getMock();
        $fileUploadMock->expects($this->any())->method('getTemporaryFilepath')->willReturn('dir/' . $temporaryFilename);

        $exception = new \Shopsys\ShopBundle\Component\Image\Processing\Exception\FileIsNotSupportedImageException($temporaryFilename);
        $imageThumbnailFactoryMock = $this->getMockBuilder(ImageThumbnailFactory::class)
            ->setMethods(['getImageThumbnail'])
            ->disableOriginalConstructor()
            ->getMock();
        $imageThumbnailFactoryMock->expects($this->once())->method('getImageThumbnail')->willThrowException($exception);

        $fileThumbnailExtension = new FileThumbnailExtension($fileUploadMock, $imageThumbnailFactoryMock);
        $fileThumbnailInfo = $fileThumbnailExtension->getFileThumbnailInfoByTemporaryFilename($temporaryFilename);

        $this->assertSame('word', $fileThumbnailInfo->getIconType());
        $this->assertNull($fileThumbnailInfo->getImageUri());
    }
}
