<?php

declare(strict_types=1);

namespace Tests\FrameworkBundle\Unit\Component\Image;

use League\Flysystem\FilesystemInterface;
use League\Flysystem\MountManager;
use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\FileUpload\FileNamingConvention;
use Shopsys\FrameworkBundle\Component\FileUpload\FileUpload;
use Shopsys\FrameworkBundle\Component\Image\Config\ImageEntityConfig;
use Shopsys\FrameworkBundle\Component\Image\Image;
use Shopsys\FrameworkBundle\Component\Image\ImageFactory;
use Shopsys\FrameworkBundle\Component\Image\Processing\ImageProcessor;
use Symfony\Component\Filesystem\Filesystem;

class ImageFactoryTest extends TestCase
{
    public function testCreateMultipleException()
    {
        $imageEntityConfig = new ImageEntityConfig('entityName', 'entityClass', [], [], ['type' => false]);

        $imageProcessorMock = $this->getMockBuilder(ImageProcessor::class)
            ->disableOriginalConstructor()
            ->getMock();

        $imageFactory = new ImageFactory($imageProcessorMock, $this->getFileUpload());

        $this->expectException(\Shopsys\FrameworkBundle\Component\Image\Exception\EntityMultipleImageException::class);
        $imageFactory->createMultiple($imageEntityConfig, 1, 'type', []);
    }

    public function testCreateMultiple()
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

        $imageFactory = new ImageFactory($imageProcessorMock, $this->getFileUpload());
        $images = $imageFactory->createMultiple($imageEntityConfig, 1, 'type', $filenames);

        $this->assertCount(2, $images);
        foreach ($images as $image) {
            $temporaryFiles = $image->getTemporaryFilesForUpload();
            $this->assertSame(1, $image->getEntityId());
            $this->assertSame('entityName', $image->getEntityName());
            $this->assertContains(array_pop($temporaryFiles)->getTemporaryFilename(), $filenames);
        }
    }

    public function testCreate()
    {
        $imageEntityConfig = new ImageEntityConfig('entityName', 'entityClass', [], [], ['type' => true]);
        $filename = 'filename.jpg';

        $imageProcessorMock = $this->getMockBuilder(ImageProcessor::class)
            ->disableOriginalConstructor()
            ->setMethods(['convertToShopFormatAndGetNewFilename'])
            ->getMock();
        $imageProcessorMock->expects($this->any())->method('convertToShopFormatAndGetNewFilename')->willReturn($filename);

        $imageFactory = new ImageFactory($imageProcessorMock, $this->getFileUpload());
        $image = $imageFactory->create($imageEntityConfig->getEntityName(), 1, 'type', $filename);
        $temporaryFiles = $image->getTemporaryFilesForUpload();

        $this->assertInstanceOf(Image::class, $image);
        $this->assertSame($filename, array_pop($temporaryFiles)->getTemporaryFilename());
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\FileUpload\FileUpload
     */
    private function getFileUpload(): FileUpload
    {
        $fileNamingConvention = new FileNamingConvention();
        $filesystem = new Filesystem();
        $mountManager = new MountManager();
        $abstractFilesystem = $this->createMock(FilesystemInterface::class);

        return new FileUpload('temporaryDir', 'uploadedFileDir', 'imageDir', $fileNamingConvention, $filesystem, $mountManager, $abstractFilesystem);
    }
}
