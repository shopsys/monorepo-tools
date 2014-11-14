<?php

namespace SS6\ShopBundle\Tests\Model\Image;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Image\Config\ImageConfig;
use SS6\ShopBundle\Model\Image\Config\ImageEntityConfig;
use SS6\ShopBundle\Model\Image\Config\ImageSizeConfig;
use SS6\ShopBundle\Model\Image\DirectoryStructureCreator;
use SS6\ShopBundle\Model\Image\ImagesEntity;
use SS6\ShopBundle\Model\Image\ImageFacade;
use Symfony\Component\Filesystem\Filesystem;

class DirectoryStructureCreatorTest extends PHPUnit_Framework_TestCase {

	public function testMakeImageDirectories() {
		$imageDir = 'imageDir';
		$imageEntityConfigByClass = array(
			new ImageEntityConfig(
				'entityName1',
				'entityClass1',
				[],
				[new ImageSizeConfig('sizeName1_1', null, null, false)]
				),
			new ImageEntityConfig(
				'entityName2',
				'entityClass2',
				['type' => [new ImageSizeConfig('sizeName2_1', null, null, false)]],
				[]
				),
		);
		$imageConfig = new ImageConfig($imageEntityConfigByClass);
		$imageFacadeMock = $this->getMock(ImageFacade::class, [], [], '', false);
		$imagesEntity = new ImagesEntity($imageDir, $imageConfig, $imageFacadeMock);
		$filesystemMock = $this->getMockBuilder(Filesystem::class)
			->setMethods(['mkdir'])
			->getMock();
		$filesystemMock
			->expects($this->once())
			->method('mkdir')
			->with($this->callback(function ($actual) {
				$expected = [
					'imageDir' . DIRECTORY_SEPARATOR
						. 'entityName1' . DIRECTORY_SEPARATOR
						. 'sizeName1_1' . DIRECTORY_SEPARATOR,
					'imageDir' . DIRECTORY_SEPARATOR
						. 'entityName2' . DIRECTORY_SEPARATOR
						. 'type' . DIRECTORY_SEPARATOR
						. 'sizeName2_1' . DIRECTORY_SEPARATOR
				];
				asort($expected);
				asort($actual);
				$this->assertEquals($expected, $actual);
				return true;
			}));

		$creator = new DirectoryStructureCreator($imageDir, $imageConfig, $imagesEntity, $filesystemMock);
		$creator->makeImageDirectories();
	}

}
