<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Image;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Image\Config\ImageConfig;
use SS6\ShopBundle\Model\Image\Config\ImageConfigDefinition;
use SS6\ShopBundle\Model\Image\Config\ImageConfigLoader;
use SS6\ShopBundle\Model\Image\ImageLocator;
use stdClass;
use Symfony\Component\Filesystem\Filesystem;

class ImageLocatorTest extends PHPUnit_Framework_TestCase {

	/**
	 * @return \SS6\ShopBundle\Model\Image\Config\ImageConfig
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	private function getBaseImageConfig() {
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
					],
					[
						ImageConfigDefinition::CONFIG_SIZE_NAME => 'SizeName_0_1',
						ImageConfigDefinition::CONFIG_SIZE_WIDTH => null,
						ImageConfigDefinition::CONFIG_SIZE_HEIGHT => null,
						ImageConfigDefinition::CONFIG_SIZE_CROP => false,
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
							],
							[
								ImageConfigDefinition::CONFIG_SIZE_NAME => null,
								ImageConfigDefinition::CONFIG_SIZE_WIDTH => 200,
								ImageConfigDefinition::CONFIG_SIZE_HEIGHT => 100,
								ImageConfigDefinition::CONFIG_SIZE_CROP => true,
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

	public function getRelativeImagePathProvider() {
		return [
			[
				'Name_1',
				'TypeName_1',
				'SizeName_1_1',
				'Name_1' . DIRECTORY_SEPARATOR . 'TypeName_1' . DIRECTORY_SEPARATOR . 'SizeName_1_1' . DIRECTORY_SEPARATOR,
			],
			[
				'Name_1',
				'TypeName_1',
				null,
				'Name_1' . DIRECTORY_SEPARATOR . 'TypeName_1' . DIRECTORY_SEPARATOR . ImageConfig::DEFAULT_SIZE_NAME . DIRECTORY_SEPARATOR,
			],
			[
				'Name_1',
				null,
				'SizeName_0_1',
				'Name_1' . DIRECTORY_SEPARATOR . 'SizeName_0_1' . DIRECTORY_SEPARATOR,
			],
			[
				'Name_1',
				null,
				null,
				'Name_1' . DIRECTORY_SEPARATOR . ImageConfig::DEFAULT_SIZE_NAME . DIRECTORY_SEPARATOR,
			],
		];
	}

	/**
	 * @dataProvider getRelativeImagePathProvider
	 */
	public function testGetRelativeImagePath($entityName, $type, $sizeName, $expectedPath) {
		$imageLocator = new ImageLocator('imageDir', $this->getBaseImageConfig());

		$this->assertSame($expectedPath, $imageLocator->getRelativeImagePath($entityName, $type, $sizeName));
	}

	public function getRelativeImagePathExceptionProvider() {
		return [
			[
				'NonexistentName',
				null,
				null,
				\SS6\ShopBundle\Model\Image\Config\Exception\ImageEntityConfigNotFoundException::class,
			],
			[
				'Name_1',
				'NonexistentTypeName',
				null,
				\SS6\ShopBundle\Model\Image\Config\Exception\ImageTypeNotFoundException::class,
			],
			[
				'Name_1',
				null,
				'NonexistentSizeName',
				\SS6\ShopBundle\Model\Image\Config\Exception\ImageSizeNotFoundException::class,
			],
		];
	}

	/**
	 * @dataProvider getRelativeImagePathExceptionProvider
	 */
	public function testGetRelativeImagePathException($entityName, $type, $sizeName, $exceptionClass) {
		$imageLocator = new ImageLocator('imageDir', $this->getBaseImageConfig());

		$this->setExpectedException($exceptionClass);
		$imageLocator->getRelativeImagePath($entityName, $type, $sizeName);
	}
}
