<?php

namespace Shopsys\ShopBundle\Tests\Unit\Component\Image\Config;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Component\Image\Config\Exception\ImageTypeNotFoundException;
use Shopsys\ShopBundle\Component\Image\Config\ImageEntityConfig;
use Shopsys\ShopBundle\Component\Image\Config\ImageSizeConfig;

class ImageEntityConfigTest extends PHPUnit_Framework_TestCase
{
    public function testGetTypeSizes() {
        $types = [
            'TypeName_1' => [
                'SizeName_1_1' => new ImageSizeConfig('SizeName_1_1', null, null, false),
                'SizeName_1_2' => new ImageSizeConfig('SizeName_1_2', null, null, false),
            ],
            'TypeName_2' => [
                'SizeName_2_1' => new ImageSizeConfig('SizeName_2_1', null, null, false),
            ],
        ];
        $sizes = [];

        $imageEntityConfig = new ImageEntityConfig('EntityName', 'EntityClass', $types, $sizes, []);

        $typeSizes = $imageEntityConfig->getSizeConfigsByType('TypeName_1');
        $this->assertSame($types['TypeName_1'], $typeSizes);
    }

    public function testGetTypeSizesNotFound() {
        $types = [
            'TypeName_1' => [
                'SizeName_1_1' => new ImageSizeConfig('SizeName_1_1', null, null, false),
                'SizeName_1_2' => new ImageSizeConfig('SizeName_1_2', null, null, false),
            ],
            'TypeName_2' => [
                'SizeName_2_1' => new ImageSizeConfig('SizeName_2_1', null, null, false),
            ],
        ];
        $sizes = [];

        $imageEntityConfig = new ImageEntityConfig('EntityName', 'EntityClass', $types, $sizes, []);

        $this->setExpectedException(ImageTypeNotFoundException::class);
        $imageEntityConfig->getSizeConfigsByType('TypeName_3');
    }

    public function testGetTypeSize() {
        $types = [
            'TypeName_1' => [
                'SizeName_1_1' => new ImageSizeConfig('SizeName_1_1', null, null, false),
                'SizeName_1_2' => new ImageSizeConfig('SizeName_1_2', null, null, false),
            ],
            'TypeName_2' => [
                ImageEntityConfig::WITHOUT_NAME_KEY => new ImageSizeConfig(null, null, null, false),
            ],
        ];
        $sizes = [
            ImageEntityConfig::WITHOUT_NAME_KEY => new ImageSizeConfig(null, null, null, false),
        ];

        $imageEntityConfig = new ImageEntityConfig('EntityName', 'EntityClass', $types, $sizes, []);

        $size1 = $imageEntityConfig->getSizeConfigByType(null, null);
        $this->assertSame($sizes[ImageEntityConfig::WITHOUT_NAME_KEY], $size1);

        $type1Size1 = $imageEntityConfig->getSizeConfigByType('TypeName_1', 'SizeName_1_1');
        $this->assertSame($types['TypeName_1']['SizeName_1_1'], $type1Size1);

        $type2Size1 = $imageEntityConfig->getSizeConfigByType('TypeName_2', null);
        $this->assertSame($types['TypeName_2'][ImageEntityConfig::WITHOUT_NAME_KEY], $type2Size1);
    }
}
