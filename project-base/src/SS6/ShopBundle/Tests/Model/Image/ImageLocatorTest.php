<?php

namespace SS6\ShopBundle\Tests\Model\Image;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Image\Config\ImageConfig;
use SS6\ShopBundle\Model\Image\ImageLocator;

class ImageLocatorTest extends PHPUnit_Framework_TestCase {

	public function testGetRelativeImagePathProvider() {
		return [
			[
				'entity',
				'type',
				'size',
				'entity' . DIRECTORY_SEPARATOR . 'type' . DIRECTORY_SEPARATOR . 'size' . DIRECTORY_SEPARATOR,
			],
			[
				'entity',
				'type',
				null,
				'entity' . DIRECTORY_SEPARATOR . 'type' . DIRECTORY_SEPARATOR . ImageConfig::DEFAULT_SIZE_NAME . DIRECTORY_SEPARATOR
			],
			[
				'entity',
				null,
				'size',
				'entity' . DIRECTORY_SEPARATOR . 'size' . DIRECTORY_SEPARATOR
			],
			[
				'entity',
				null,
				null,
				'entity' . DIRECTORY_SEPARATOR . ImageConfig::DEFAULT_SIZE_NAME . DIRECTORY_SEPARATOR
			],
		];
	}

	/**
	 * @dataProvider testGetRelativeImagePathProvider
	 */
	public function testGetRelativeImagePath($entityName, $type, $sizeName, $expectedPath) {
		$imageLocator = new ImageLocator('imageDir');

		$this->assertEquals($expectedPath, $imageLocator->getRelativeImagePath($entityName, $type, $sizeName));
	}
}
