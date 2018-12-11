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
