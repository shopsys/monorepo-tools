<?php

namespace Shopsys\ShopBundle\Tests\Unit\Component\Image;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Component\FileUpload\FileNamingConvention;
use Shopsys\ShopBundle\Component\FileUpload\FileUpload;
use Shopsys\ShopBundle\Component\Image\Config\ImageEntityConfig;
use Shopsys\ShopBundle\Component\Image\Image;
use Shopsys\ShopBundle\Component\Image\ImageService;
use Shopsys\ShopBundle\Component\Image\Processing\ImageProcessingService;
use Symfony\Component\Filesystem\Filesystem;

class ImageServiceTest extends PHPUnit_Framework_TestCase {

    public function testGetUploadedImagesException() {
        $imageEntityConfig = new ImageEntityConfig('entityName', 'entityClass', [], [], ['type' => false]);

        $imageProcessingServiceMock = $this->getMockBuilder(ImageProcessingService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $imageService = new ImageService($imageProcessingServiceMock, $this->getFileUpload());

        $this->setExpectedException(\Shopsys\ShopBundle\Component\Image\Exception\EntityMultipleImageException::class);
        $imageService->getUploadedImages($imageEntityConfig, 1, [], 'type');
    }

    public function testGetUploadedImages() {
        $imageEntityConfig = new ImageEntityConfig('entityName', 'entityClass', [], [], ['type' => true]);
        $filenames = ['filename1.jpg', 'filename2.png'];

        $imageProcessingServiceMock = $this->getMockBuilder(ImageProcessingService::class)
            ->disableOriginalConstructor()
            ->setMethods(['convertToShopFormatAndGetNewFilename'])
            ->getMock();
        $imageProcessingServiceMock->expects($this->any())->method('convertToShopFormatAndGetNewFilename')
            ->willReturnCallback(function ($filepath) {
                return pathinfo($filepath, PATHINFO_BASENAME);
            });

        $imageService = new ImageService($imageProcessingServiceMock, $this->getFileUpload());
        $images = $imageService->getUploadedImages($imageEntityConfig, 1, $filenames, 'type');

        $this->assertCount(2, $images);
        foreach ($images as $image) {
            /* @var $image \Shopsys\ShopBundle\Component\Image\Image */
            $temporaryFiles = $image->getTemporaryFilesForUpload();
            $this->assertSame(1, $image->getEntityId());
            $this->assertSame('entityName', $image->getEntityName());
            $this->assertContains(array_pop($temporaryFiles)->getTemporaryFilename(), $filenames);
        }
    }

    public function testCreateImage() {
        $imageEntityConfig = new ImageEntityConfig('entityName', 'entityClass', [], [], ['type' => true]);
        $filename = 'filename.jpg';

        $imageProcessingServiceMock = $this->getMockBuilder(ImageProcessingService::class)
            ->disableOriginalConstructor()
            ->setMethods(['convertToShopFormatAndGetNewFilename'])
            ->getMock();
        $imageProcessingServiceMock->expects($this->any())->method('convertToShopFormatAndGetNewFilename')->willReturn($filename);

        $imageService = new ImageService($imageProcessingServiceMock, $this->getFileUpload());
        $image = $imageService->createImage($imageEntityConfig, 1, $filename, 'type');
        $temporaryFiles = $image->getTemporaryFilesForUpload();

        $this->assertInstanceOf(Image::class, $image);
        $this->assertSame($filename, array_pop($temporaryFiles)->getTemporaryFilename());
    }

    /**
     * @return \Shopsys\ShopBundle\Component\FileUpload\FileUpload
     */
    private function getFileUpload() {
        $fileNamingConvention = new FileNamingConvention();
        $filesystem = new Filesystem();

        return new FileUpload('temporaryDir', 'uploadedFileDir', 'imageDir', $fileNamingConvention, $filesystem);
    }

}
