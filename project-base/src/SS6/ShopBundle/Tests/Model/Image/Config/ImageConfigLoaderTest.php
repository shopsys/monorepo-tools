<?php

namespace SS6\ShopBundle\Tests\Model\Image\Config;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Image\Config\ImageConfig;
use SS6\ShopBundle\Model\Image\Config\ImageConfigDefinition;
use SS6\ShopBundle\Model\Image\Config\ImageConfigLoader;
use SS6\ShopBundle\Model\Image\Config\Exception\DuplicateEntityNameException;
use SS6\ShopBundle\Model\Image\Config\Exception\DuplicateSizeNameException;
use SS6\ShopBundle\Model\Image\Config\Exception\DuplicateTypeNameException;
use SS6\ShopBundle\Model\Image\Config\ImageSizeConfig;
use Symfony\Component\Filesystem\Filesystem;

class ImageConfigLoaderTest extends PHPUnit_Framework_TestCase {

	public function testLoadFromArrayDuplicateEntityName() {
		$inputConfig = array(
			array(
				ImageConfigDefinition::CONFIG_ENTITY_NAME => 'Name_1',
				ImageConfigDefinition::CONFIG_CLASS => 'Class_1',
				ImageConfigDefinition::CONFIG_SIZES => array(),
				ImageConfigDefinition::CONFIG_TYPES => array(),
			),
			array(
				ImageConfigDefinition::CONFIG_ENTITY_NAME => 'Name_1',
				ImageConfigDefinition::CONFIG_CLASS => 'Class_2',
				ImageConfigDefinition::CONFIG_SIZES => array(),
				ImageConfigDefinition::CONFIG_TYPES => array(),
			),
		);

		$filesystem = new Filesystem();
		$imageConfigLoader = new ImageConfigLoader($filesystem);

		$previousException = null;
		try {
			$imageConfigLoader->loadFromArray($inputConfig);
		} catch (\SS6\ShopBundle\Model\Image\Config\Exception\EntityParseException $exception) {
			$previousException = $exception->getPrevious();
		}

		$this->assertInstanceOf(DuplicateEntityNameException::class, $previousException);
	}

	public function testLoadFromArrayDuplicateEntityClass() {
		$inputConfig = array(
			array(
				ImageConfigDefinition::CONFIG_CLASS => 'Class_1',
				ImageConfigDefinition::CONFIG_ENTITY_NAME => 'Name_1',
				ImageConfigDefinition::CONFIG_SIZES => array(),
				ImageConfigDefinition::CONFIG_TYPES => array(),
			),
			array(
				ImageConfigDefinition::CONFIG_ENTITY_NAME => 'Name_2',
				ImageConfigDefinition::CONFIG_CLASS => 'Class_1',
				ImageConfigDefinition::CONFIG_SIZES => array(),
				ImageConfigDefinition::CONFIG_TYPES => array(),
			),
		);

		$filesystem = new Filesystem();
		$imageConfigLoader = new ImageConfigLoader($filesystem);

		$previousException = null;
		try {
			$imageConfigLoader->loadFromArray($inputConfig);
		} catch (\SS6\ShopBundle\Model\Image\Config\Exception\EntityParseException $exception) {
			$previousException = $exception->getPrevious();
		}

		$this->assertInstanceOf(DuplicateEntityNameException::class, $previousException);
	}

	public function testLoadFromArrayDuplicateNullSizeName() {
		$inputConfig = array(
			array(
				ImageConfigDefinition::CONFIG_CLASS => 'Class_1',
				ImageConfigDefinition::CONFIG_ENTITY_NAME => 'Name_1',
				ImageConfigDefinition::CONFIG_SIZES => array(
					array(
						ImageConfigDefinition::CONFIG_SIZE_NAME => null,
						ImageConfigDefinition::CONFIG_SIZE_WIDTH => null,
						ImageConfigDefinition::CONFIG_SIZE_HEIGHT => null,
						ImageConfigDefinition::CONFIG_SIZE_CROP => false,
					),
					array(
						ImageConfigDefinition::CONFIG_SIZE_NAME => null,
						ImageConfigDefinition::CONFIG_SIZE_WIDTH => null,
						ImageConfigDefinition::CONFIG_SIZE_HEIGHT => null,
						ImageConfigDefinition::CONFIG_SIZE_CROP => false,
					),
				),
				ImageConfigDefinition::CONFIG_TYPES => array(),
			),
		);

		$filesystem = new Filesystem();
		$imageConfigLoader = new ImageConfigLoader($filesystem);

		$previousException = null;
		try {
			$imageConfigLoader->loadFromArray($inputConfig);
		} catch (\SS6\ShopBundle\Model\Image\Config\Exception\EntityParseException $exception) {
			$previousException = $exception->getPrevious();
		}

		$this->assertInstanceOf(DuplicateSizeNameException::class, $previousException);
	}

	public function testLoadFromArrayDuplicateTypeName() {
		$inputConfig = array(
			array(
				ImageConfigDefinition::CONFIG_CLASS => 'Class_1',
				ImageConfigDefinition::CONFIG_ENTITY_NAME => 'Name_1',
				ImageConfigDefinition::CONFIG_SIZES => array(),
				ImageConfigDefinition::CONFIG_TYPES => array(
					array(
						ImageConfigDefinition::CONFIG_TYPE_NAME => 'TypeName_1',
						ImageConfigDefinition::CONFIG_SIZES => array(),
					),
					array(
						ImageConfigDefinition::CONFIG_TYPE_NAME => 'TypeName_1',
						ImageConfigDefinition::CONFIG_SIZES => array(),
					),
				),
			),
		);

		$filesystem = new Filesystem();
		$imageConfigLoader = new ImageConfigLoader($filesystem);

		$previousException = null;
		try {
			$imageConfigLoader->loadFromArray($inputConfig);
		} catch (\SS6\ShopBundle\Model\Image\Config\Exception\EntityParseException $exception) {
			$previousException = $exception->getPrevious();
		}

		$this->assertInstanceOf(DuplicateTypeNameException::class, $previousException);
	}

	public function testLoadFromArray() {
		$inputConfig = array(
			array(
				ImageConfigDefinition::CONFIG_CLASS => 'Class_1',
				ImageConfigDefinition::CONFIG_ENTITY_NAME => 'Name_1',
				ImageConfigDefinition::CONFIG_SIZES => array(),
				ImageConfigDefinition::CONFIG_TYPES => array(
					array(
						ImageConfigDefinition::CONFIG_TYPE_NAME => 'TypeName_1',
						ImageConfigDefinition::CONFIG_SIZES => array(
							array(
								ImageConfigDefinition::CONFIG_SIZE_NAME => 'SizeName_1',
								ImageConfigDefinition::CONFIG_SIZE_WIDTH => null,
								ImageConfigDefinition::CONFIG_SIZE_HEIGHT => null,
								ImageConfigDefinition::CONFIG_SIZE_CROP => false,
							),
							array(
								ImageConfigDefinition::CONFIG_SIZE_NAME => 'SizeName_2',
								ImageConfigDefinition::CONFIG_SIZE_WIDTH => 200,
								ImageConfigDefinition::CONFIG_SIZE_HEIGHT => 100,
								ImageConfigDefinition::CONFIG_SIZE_CROP => true,
							),
						),
					),
					array(
						ImageConfigDefinition::CONFIG_TYPE_NAME => 'TypeName_2',
						ImageConfigDefinition::CONFIG_SIZES => array(),
					),
				),
			),
		);

		$filesystem = new Filesystem();
		$imageConfigLoader = new ImageConfigLoader($filesystem);

		$preparedConfig = $imageConfigLoader->loadFromArray($inputConfig);

		$imageEntityConfig = $preparedConfig[$inputConfig[0][ImageConfigDefinition::CONFIG_CLASS]];
		$this->assertEquals('Class_1', $imageEntityConfig->getEntityClass());
		$this->assertEquals('Name_1', $imageEntityConfig->getEntityName());

		$imageSize = $imageEntityConfig->getTypeSize('TypeName_1', 'SizeName_2');

		$this->assertEquals('SizeName_2', $imageSize->getName());
		$this->assertEquals(200, $imageSize->getWidth());
		$this->assertEquals(100, $imageSize->getHeight());
		$this->assertEquals(true, $imageSize->getCrop());
	}

	public function testLoadFromArrayOriginalSize() {
		$inputConfig = array(
			array(
				ImageConfigDefinition::CONFIG_CLASS => 'Class_1',
				ImageConfigDefinition::CONFIG_ENTITY_NAME => 'Name_1',
				ImageConfigDefinition::CONFIG_SIZES => array(),
				ImageConfigDefinition::CONFIG_TYPES => array(),
			),
		);

		$filesystem = new Filesystem();
		$imageConfigLoader = new ImageConfigLoader($filesystem);

		$preparedConfig = $imageConfigLoader->loadFromArray($inputConfig);

		$imageEntityConfig = $preparedConfig[$inputConfig[0][ImageConfigDefinition::CONFIG_CLASS]];
		$imageSize = $imageEntityConfig->getTypeSize(null, ImageConfig::ORIGINAL_SIZE_NAME);

		$this->assertInstanceOf(ImageSizeConfig::class, $imageSize);
		$this->assertNull($imageSize->getHeight());
		$this->assertNull($imageSize->getWidth());
		$this->assertFalse($imageSize->getCrop());
	}

	public function testLoadFromArrayExistsOriginalSize() {
		$inputConfig = array(
			array(
				ImageConfigDefinition::CONFIG_CLASS => 'Class_1',
				ImageConfigDefinition::CONFIG_ENTITY_NAME => 'Name_1',
				ImageConfigDefinition::CONFIG_SIZES => array(
					array(
						ImageConfigDefinition::CONFIG_SIZE_NAME => ImageConfig::ORIGINAL_SIZE_NAME,
						ImageConfigDefinition::CONFIG_SIZE_WIDTH => 200,
						ImageConfigDefinition::CONFIG_SIZE_HEIGHT => 100,
						ImageConfigDefinition::CONFIG_SIZE_CROP => true,
					)
				),
				ImageConfigDefinition::CONFIG_TYPES => array(),
			),
		);

		$filesystem = new Filesystem();
		$imageConfigLoader = new ImageConfigLoader($filesystem);

		$preparedConfig = $imageConfigLoader->loadFromArray($inputConfig);

		$imageEntityConfig = $preparedConfig[$inputConfig[0][ImageConfigDefinition::CONFIG_CLASS]];
		$this->assertCount(1, $imageEntityConfig->getSizes());

		$imageSize = $imageEntityConfig->getTypeSize(null, ImageConfig::ORIGINAL_SIZE_NAME);

		$this->assertInstanceOf(ImageSizeConfig::class, $imageSize);
		$this->assertEquals(100, $imageSize->getHeight());
		$this->assertEquals(200, $imageSize->getWidth());
		$this->assertTrue($imageSize->getCrop());
	}

}
