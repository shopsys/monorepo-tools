<?php

namespace SS6\ShopBundle\Tests\Model\Image;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Image\Config\ImageConfig;
use SS6\ShopBundle\Model\Image\Config\ImageEntityConfig;
use SS6\ShopBundle\Model\Image\Exception\EntityFilenameMethodNotFoundException;
use SS6\ShopBundle\Model\Image\ImagesEntity;
use stdClass;

class ImageConfigTest extends PHPUnit_Framework_TestCase {

	public function testGetEntityNameFilenameMethodNotFound() {
		$imageDir = '';

		$imageEntityConfig = new ImageEntityConfig(
			'EntityName',
			stdClass::class,
			array('TypeName_1' => 'getId'),
			array(),
			array()
		);

		$imageConfigMock = $this->getMockBuilder(ImageConfig::class)
			->setMethods(array('__construct', 'getImageEntityConfig'))
			->disableOriginalConstructor()
			->getMock();
		$imageConfigMock
			->expects($this->once())->method('getImageEntityConfig')->will($this->returnValue($imageEntityConfig));

		$imagesEntity = new ImagesEntity($imageDir, $imageConfigMock);

		$this->setExpectedException(EntityFilenameMethodNotFoundException::class);
		$imagesEntity->getRelativeImageFilepath(new stdClass(), 'TypeName_1', null);
	}

	public function testGetRelativeImagePathProvider() {
		return array(
			array('entity', 'type', 'size', 'entity' . DIRECTORY_SEPARATOR . 'type' . DIRECTORY_SEPARATOR . 'size' . DIRECTORY_SEPARATOR),
			array('entity', 'type', null, 'entity' . DIRECTORY_SEPARATOR . 'type' . DIRECTORY_SEPARATOR),
			array('entity', null, 'size', 'entity' . DIRECTORY_SEPARATOR . 'size' . DIRECTORY_SEPARATOR),
			array('entity', null, null, 'entity' . DIRECTORY_SEPARATOR),
		);
	}

	/**
	 * @dataProvider testGetRelativeImagePathProvider
	 */
	public function testGetRelativeImagePath($entityName, $type, $sizeName, $expectedPath) {
		$imageConfig = new ImageConfig(array());
		$imagesEntity = new ImagesEntity('imageDir', $imageConfig);

		$this->assertEquals($expectedPath, $imagesEntity->getRelativeImagePath($entityName, $type, $sizeName));
	}
}
