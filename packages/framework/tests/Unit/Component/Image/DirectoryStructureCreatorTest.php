<?php

namespace Tests\FrameworkBundle\Unit\Component\Image;

use League\Flysystem\FilesystemInterface;
use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Image\Config\ImageConfig;
use Shopsys\FrameworkBundle\Component\Image\Config\ImageEntityConfig;
use Shopsys\FrameworkBundle\Component\Image\Config\ImageSizeConfig;
use Shopsys\FrameworkBundle\Component\Image\DirectoryStructureCreator;
use Shopsys\FrameworkBundle\Component\Image\ImageLocator;

class DirectoryStructureCreatorTest extends TestCase
{
    public function testMakeImageDirectories()
    {
        $imageDir = 'imageDir/';
        $domainImageDir = 'domainImageDir';
        $imageEntityConfigByClass = [
            new ImageEntityConfig(
                'entityName1',
                'entityClass1',
                [],
                ['sizeName1_1' => new ImageSizeConfig('sizeName1_1', null, null, false, null)],
                []
            ),
            new ImageEntityConfig(
                'entityName2',
                'entityClass2',
                ['type' => ['sizeName2_1' => new ImageSizeConfig('sizeName2_1', null, null, false, null)]],
                [],
                []
            ),
        ];
        $imageConfig = new ImageConfig($imageEntityConfigByClass);
        $filesystemMock = $this->createMock(FilesystemInterface::class);
        $filesystemMock
            ->method('createDir')
            ->withConsecutive(
                ['imageDir/entityName1/sizeName1_1/'],
                ['imageDir/entityName2/type/sizeName2_1/']
            );
        $imageLocator = new ImageLocator($imageDir, $imageConfig, $filesystemMock);
        $creator = new DirectoryStructureCreator($imageDir, $domainImageDir, $imageConfig, $imageLocator, $filesystemMock);
        $creator->makeImageDirectories();
    }
}
