<?php

namespace SS6\ShopBundle\Tests\Model\Image\Config;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Image\Config\Exception\ImageTypeNotFoundException;
use SS6\ShopBundle\Model\Image\Config\ImageEntityConfig;
use SS6\ShopBundle\Model\Image\Config\ImageSizeConfig;

class ImageEntityConfigTest extends PHPUnit_Framework_TestCase {

	public function testGetTypeSizes() {
		$types = array(
			'TypeName_1' => array(
				'SizeName_1_1' => new ImageSizeConfig('SizeName_1_1', null, null, false),
				'SizeName_1_2' => new ImageSizeConfig('SizeName_1_2', null, null, false),
			),
			'TypeName_2' => array(
				'SizeName_2_1' => new ImageSizeConfig('SizeName_2_1', null, null, false),
			),
		);
		$sizes = array();

		$imageEntityConfig = new ImageEntityConfig('EntityName', 'EntityClass', $types, $sizes, []);

		$typeSizes = $imageEntityConfig->getTypeSizes('TypeName_1');
		$this->assertEquals($types['TypeName_1'], $typeSizes);
	}

	public function testGetTypeSizesNotFound() {
		$types = array(
			'TypeName_1' => array(
				'SizeName_1_1' => new ImageSizeConfig('SizeName_1_1', null, null, false),
				'SizeName_1_2' => new ImageSizeConfig('SizeName_1_2', null, null, false),
			),
			'TypeName_2' => array(
				'SizeName_2_1' => new ImageSizeConfig('SizeName_2_1', null, null, false),
			),
		);
		$sizes = array();

		$imageEntityConfig = new ImageEntityConfig('EntityName', 'EntityClass', $types, $sizes, []);

		$this->setExpectedException(ImageTypeNotFoundException::class);
		$imageEntityConfig->getTypeSizes('TypeName_3');
	}

	public function testGetTypeSize() {
		$types = array(
			'TypeName_1' => array(
				'SizeName_1_1' => new ImageSizeConfig('SizeName_1_1', null, null, false),
				'SizeName_1_2' => new ImageSizeConfig('SizeName_1_2', null, null, false),
			),
			'TypeName_2' => array(
				ImageEntityConfig::WITHOUT_NAME_KEY => new ImageSizeConfig(null, null, null, false),
			),
		);
		$sizes = array(
			ImageEntityConfig::WITHOUT_NAME_KEY => new ImageSizeConfig(null, null, null, false),
		);

		$imageEntityConfig = new ImageEntityConfig('EntityName', 'EntityClass', $types, $sizes, []);

		$size1 = $imageEntityConfig->getTypeSize(null, null);
		$this->assertEquals($sizes[ImageEntityConfig::WITHOUT_NAME_KEY], $size1);

		$type1Size1 = $imageEntityConfig->getTypeSize('TypeName_1', 'SizeName_1_1');
		$this->assertEquals($types['TypeName_1']['SizeName_1_1'], $type1Size1);

		$type2Size1 = $imageEntityConfig->getTypeSize('TypeName_2', null);
		$this->assertEquals($types['TypeName_2'][ImageEntityConfig::WITHOUT_NAME_KEY], $type2Size1);
	}

}
