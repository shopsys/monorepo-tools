<?php

namespace Tests\ShopBundle\Unit\Component\Image;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Image\Config\ImageConfig;
use Shopsys\FrameworkBundle\Component\Image\Config\ImageEntityConfig;
use Shopsys\FrameworkBundle\Component\Image\Config\ImageSizeConfig;
use Shopsys\FrameworkBundle\Component\Image\DirectoryStructureCreator;
use Shopsys\FrameworkBundle\Component\Image\ImageLocator;
use Symfony\Component\Filesystem\Filesystem;

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
        $imageLocator = new ImageLocator($imageDir, $imageConfig);
        $filesystemMock = $this->getMockBuilder(Filesystem::class)
            ->setMethods(['mkdir'])
            ->getMock();
        $filesystemMock
            ->expects($this->once())
            ->method('mkdir')
            ->with($this->callback(function ($actual) {
                $expected = [
                    'imageDir/entityName1/sizeName1_1/',
                    'imageDir/entityName2/type/sizeName2_1/',
                    'domainImageDir',
                ];
                asort($expected);
                asort($actual);
                $this->assertSame($expected, $actual);
                return true;
            }));

        $creator = new DirectoryStructureCreator($imageDir, $domainImageDir, $imageConfig, $imageLocator, $filesystemMock);
        $creator->makeImageDirectories();
    }
}
