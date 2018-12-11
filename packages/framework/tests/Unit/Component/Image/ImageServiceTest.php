<?php

namespace Tests\FrameworkBundle\Unit\Component\Image;

use League\Flysystem\FilesystemInterface;
use League\Flysystem\MountManager;
use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\FileUpload\FileNamingConvention;
use Shopsys\FrameworkBundle\Component\FileUpload\FileUpload;
use Shopsys\FrameworkBundle\Component\Image\Config\ImageEntityConfig;
use Shopsys\FrameworkBundle\Component\Image\ImageFactory;
use Shopsys\FrameworkBundle\Component\Image\ImageService;
use Shopsys\FrameworkBundle\Component\Image\Processing\ImageProcessor;
use Symfony\Component\Filesystem\Filesystem;

class ImageServiceTest extends TestCase
{
    public function testGetUploadedImagesException()
    {
        $imageEntityConfig = new ImageEntityConfig('entityName', 'entityClass', [], [], ['type' => false]);

        $imageProcessorMock = $this->getMockBuilder(ImageProcessor::class)
            ->disableOriginalConstructor()
            ->getMock();

        $imageService = new ImageService(new ImageFactory($imageProcessorMock, $this->getFileUpload()));

        $this->expectException(\Shopsys\FrameworkBundle\Component\Image\Exception\EntityMultipleImageException::class);
        $imageService->getUploadedImages($imageEntityConfig, 1, [], 'type');
    }

    public function testGetUploadedImages()
    {
        $imageEntityConfig = new ImageEntityConfig('entityName', 'entityClass', [], [], ['type' => true]);
        $filenames = ['filename1.jpg', 'filename2.png'];

        $imageProcessorMock = $this->getMockBuilder(ImageProcessor::class)
            ->disableOriginalConstructor()
            ->setMethods(['convertToShopFormatAndGetNewFilename'])
            ->getMock();
        $imageProcessorMock->expects($this->any())->method('convertToShopFormatAndGetNewFilename')
            ->willReturnCallback(function ($filepath) {
                return pathinfo($filepath, PATHINFO_BASENAME);
            });

        $imageService = new ImageService(new ImageFactory($imageProcessorMock, $this->getFileUpload()));
        $images = $imageService->getUploadedImages($imageEntityConfig, 1, $filenames, 'type');

        $this->assertCount(2, $images);
        foreach ($images as $image) {
            $temporaryFiles = $image->getTemporaryFilesForUpload();
            $this->assertSame(1, $image->getEntityId());
            $this->assertSame('entityName', $image->getEntityName());
            $this->assertContains(array_pop($temporaryFiles)->getTemporaryFilename(), $filenames);
        }
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\FileUpload\FileUpload
     */
    private function getFileUpload()
    {
        $fileNamingConvention = new FileNamingConvention();
        $filesystem = new Filesystem();
        $mountManager = new MountManager();
        $abstractFilesystem = $this->createMock(FilesystemInterface::class);

        return new FileUpload('temporaryDir', 'uploadedFileDir', 'imageDir', $fileNamingConvention, $filesystem, $mountManager, $abstractFilesystem);
    }
}
