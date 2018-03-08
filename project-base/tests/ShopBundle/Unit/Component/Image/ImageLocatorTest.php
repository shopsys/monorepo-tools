<?php

namespace Tests\ShopBundle\Unit\Component\Image;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Image\Config\ImageConfig;
use Shopsys\FrameworkBundle\Component\Image\Config\ImageConfigDefinition;
use Shopsys\FrameworkBundle\Component\Image\Config\ImageConfigLoader;
use Shopsys\FrameworkBundle\Component\Image\ImageLocator;
use stdClass;
use Symfony\Component\Filesystem\Filesystem;

class ImageLocatorTest extends TestCase
{
    /**
     * @return \Shopsys\FrameworkBundle\Component\Image\Config\ImageConfig
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private function getBaseImageConfig()
    {
        $inputConfig = [
            [
                ImageConfigDefinition::CONFIG_CLASS => stdClass::class,
                ImageConfigDefinition::CONFIG_ENTITY_NAME => 'Name_1',
                ImageConfigDefinition::CONFIG_MULTIPLE => false,
                ImageConfigDefinition::CONFIG_SIZES => [
                    [
                        ImageConfigDefinition::CONFIG_SIZE_NAME => null,
                        ImageConfigDefinition::CONFIG_SIZE_WIDTH => null,
                        ImageConfigDefinition::CONFIG_SIZE_HEIGHT => null,
                        ImageConfigDefinition::CONFIG_SIZE_CROP => false,
                        ImageConfigDefinition::CONFIG_SIZE_OCCURRENCE => null,
                    ],
                    [
                        ImageConfigDefinition::CONFIG_SIZE_NAME => 'SizeName_0_1',
                        ImageConfigDefinition::CONFIG_SIZE_WIDTH => null,
                        ImageConfigDefinition::CONFIG_SIZE_HEIGHT => null,
                        ImageConfigDefinition::CONFIG_SIZE_CROP => false,
                        ImageConfigDefinition::CONFIG_SIZE_OCCURRENCE => null,
                    ],
                ],
                ImageConfigDefinition::CONFIG_TYPES => [
                    [
                        ImageConfigDefinition::CONFIG_TYPE_NAME => 'TypeName_1',
                        ImageConfigDefinition::CONFIG_MULTIPLE => false,
                        ImageConfigDefinition::CONFIG_SIZES => [
                            [
                                ImageConfigDefinition::CONFIG_SIZE_NAME => 'SizeName_1_1',
                                ImageConfigDefinition::CONFIG_SIZE_WIDTH => null,
                                ImageConfigDefinition::CONFIG_SIZE_HEIGHT => null,
                                ImageConfigDefinition::CONFIG_SIZE_CROP => false,
                                ImageConfigDefinition::CONFIG_SIZE_OCCURRENCE => null,
                            ],
                            [
                                ImageConfigDefinition::CONFIG_SIZE_NAME => null,
                                ImageConfigDefinition::CONFIG_SIZE_WIDTH => 200,
                                ImageConfigDefinition::CONFIG_SIZE_HEIGHT => 100,
                                ImageConfigDefinition::CONFIG_SIZE_CROP => true,
                                ImageConfigDefinition::CONFIG_SIZE_OCCURRENCE => null,
                            ],
                        ],
                    ],
                    [
                        ImageConfigDefinition::CONFIG_TYPE_NAME => 'TypeName_2',
                        ImageConfigDefinition::CONFIG_MULTIPLE => false,
                        ImageConfigDefinition::CONFIG_SIZES => [],
                    ],
                ],
            ],
        ];

        $filesystem = new Filesystem();
        $imageConfigLoader = new ImageConfigLoader($filesystem);
        $imageEntityConfigByClass = $imageConfigLoader->loadFromArray($inputConfig);

        return new ImageConfig($imageEntityConfigByClass);
    }

    public function getRelativeImagePathProvider()
    {
        return [
            [
                'Name_1',
                'TypeName_1',
                'SizeName_1_1',
                'Name_1/TypeName_1/SizeName_1_1/',
            ],
            [
                'Name_1',
                'TypeName_1',
                null,
                'Name_1/TypeName_1/' . ImageConfig::DEFAULT_SIZE_NAME . '/',
            ],
            [
                'Name_1',
                null,
                'SizeName_0_1',
                'Name_1/SizeName_0_1/',
            ],
            [
                'Name_1',
                null,
                null,
                'Name_1/' . ImageConfig::DEFAULT_SIZE_NAME . '/',
            ],
        ];
    }

    /**
     * @dataProvider getRelativeImagePathProvider
     */
    public function testGetRelativeImagePath($entityName, $type, $sizeName, $expectedPath)
    {
        $imageLocator = new ImageLocator('imageDir', $this->getBaseImageConfig());

        $this->assertSame($expectedPath, $imageLocator->getRelativeImagePath($entityName, $type, $sizeName));
    }

    public function getRelativeImagePathExceptionProvider()
    {
        return [
            [
                'NonexistentName',
                null,
                null,
                \Shopsys\FrameworkBundle\Component\Image\Config\Exception\ImageEntityConfigNotFoundException::class,
            ],
            [
                'Name_1',
                'NonexistentTypeName',
                null,
                \Shopsys\FrameworkBundle\Component\Image\Config\Exception\ImageTypeNotFoundException::class,
            ],
            [
                'Name_1',
                null,
                'NonexistentSizeName',
                \Shopsys\FrameworkBundle\Component\Image\Config\Exception\ImageSizeNotFoundException::class,
            ],
        ];
    }

    /**
     * @dataProvider getRelativeImagePathExceptionProvider
     */
    public function testGetRelativeImagePathException($entityName, $type, $sizeName, $exceptionClass)
    {
        $imageLocator = new ImageLocator('imageDir', $this->getBaseImageConfig());

        $this->expectException($exceptionClass);
        $imageLocator->getRelativeImagePath($entityName, $type, $sizeName);
    }
}
