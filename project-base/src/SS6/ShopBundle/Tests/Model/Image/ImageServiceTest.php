<?php

namespace SS6\ShopBundle\Tests\Model\Image;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Image\Config\ImageEntityConfig;
use SS6\ShopBundle\Model\Image\Image;
use SS6\ShopBundle\Model\Image\ImageService;

class ImageServiceTest extends PHPUnit_Framework_TestCase {

	public function testGetUploadedImagesException() {
		$imageEntityConfig = new ImageEntityConfig('entityName', 'entityClass', [], [], ['type' => false]);

		$imageService = new ImageService();

		$this->setExpectedException(\SS6\ShopBundle\Model\Image\Exception\EntityMultipleImageException::class);
		$imageService->getUploadedImages($imageEntityConfig, 1, [], 'type');
	}

	public function testGetUploadedImages() {
		$imageEntityConfig = new ImageEntityConfig('entityName', 'entityClass', [], [], ['type' => true]);
		$filenames = ['filename1.jpg', 'filename2.jpg'];

		$imageService = new ImageService();
		$images = $imageService->getUploadedImages($imageEntityConfig, 1, $filenames, 'type');

		$this->assertCount(2, $images);
		foreach ($images as $image) {
			/* @var $image \SS6\ShopBundle\Model\Image\Image */
			$temporaryFiles = $image->getTemporaryFilesForUpload();
			$this->assertEquals(1, $image->getEntityId());
			$this->assertEquals('entityName', $image->getEntityName());
			$this->assertContains(array_pop($temporaryFiles)->getTemporaryFilename(), ['filename1.jpg', 'filename2.jpg']);
		}
	}

	public function testeditImageOrCreateNewEdit() {
		$imageEntityConfig = new ImageEntityConfig('entityName', 'entityClass', [], [], ['type' => true]);
		$oldImage = new Image('entityName', 1, 'type', null);

		$imageService = new ImageService();
		$image = $imageService->editImageOrCreateNew($imageEntityConfig, 1, 'filename.jpg', 'type', $oldImage);
		$temporaryFiles = $image->getTemporaryFilesForUpload();

		$this->assertEquals($oldImage, $image);
		$this->assertEquals('filename.jpg', array_pop($temporaryFiles)->getTemporaryFilename());
	}

	public function testeditImageOrCreateNewNewt() {
		$imageEntityConfig = new ImageEntityConfig('entityName', 'entityClass', [], [], ['type' => true]);

		$imageService = new ImageService();
		$image = $imageService->editImageOrCreateNew($imageEntityConfig, 1, 'filename.jpg', 'type', null);
		$temporaryFiles = $image->getTemporaryFilesForUpload();

		$this->assertInstanceOf(Image::class, $image);
		$this->assertEquals('filename.jpg', array_pop($temporaryFiles)->getTemporaryFilename());
	}


}
