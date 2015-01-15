<?php

namespace SS6\ShopBundle\Tests\Model\Image;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\FileUpload\FileNamingConvention;
use SS6\ShopBundle\Model\FileUpload\FileUpload;
use SS6\ShopBundle\Model\Image\Config\ImageEntityConfig;
use SS6\ShopBundle\Model\Image\Image;
use SS6\ShopBundle\Model\Image\ImageService;
use SS6\ShopBundle\Model\Image\Processing\ImageProcessingService;
use Symfony\Component\Filesystem\Filesystem;

class ImageServiceTest extends PHPUnit_Framework_TestCase {

	public function testGetUploadedImagesException() {
		$imageEntityConfig = new ImageEntityConfig('entityName', 'entityClass', [], [], ['type' => false]);

		$imageProcessingServiceMock = $this->getMockBuilder(ImageProcessingService::class)
			->disableOriginalConstructor()
			->getMock();

		$imageService = new ImageService($imageProcessingServiceMock, $this->getFileUpload());

		$this->setExpectedException(\SS6\ShopBundle\Model\Image\Exception\EntityMultipleImageException::class);
		$imageService->getUploadedImages($imageEntityConfig, 1, [], 'type');
	}

	public function testGetUploadedImages() {
		$imageEntityConfig = new ImageEntityConfig('entityName', 'entityClass', [], [], ['type' => true]);
		$filenames = ['filename1.jpg', 'filename2.png'];

		$imageProcessingServiceMock = $this->getMockBuilder(ImageProcessingService::class)
			->disableOriginalConstructor()
			->setMethods(['convertToShopFormatAndGetNewFilename'])
			->getMock();
		$imageProcessingServiceMock->expects($this->any())->method('convertToShopFormatAndGetNewFilename')
			->willReturnCallback(function ($filepath) {
				return pathinfo($filepath, PATHINFO_BASENAME);
			});

		$imageService = new ImageService($imageProcessingServiceMock, $this->getFileUpload());
		$images = $imageService->getUploadedImages($imageEntityConfig, 1, $filenames, 'type');

		$this->assertCount(2, $images);
		foreach ($images as $image) {
			/* @var $image \SS6\ShopBundle\Model\Image\Image */
			$temporaryFiles = $image->getTemporaryFilesForUpload();
			$this->assertEquals(1, $image->getEntityId());
			$this->assertEquals('entityName', $image->getEntityName());
			$this->assertContains(array_pop($temporaryFiles)->getTemporaryFilename(), $filenames);
		}
	}

	public function testeditImageOrCreateNewEdit() {
		$imageEntityConfig = new ImageEntityConfig('entityName', 'entityClass', [], [], ['type' => true]);
		$oldImage = new Image('entityName', 1, 'type', null);
		$filename = 'filename.jpg';

		$imageProcessingServiceMock = $this->getMockBuilder(ImageProcessingService::class)
			->disableOriginalConstructor()
			->setMethods(['convertToShopFormatAndGetNewFilename'])
			->getMock();
		$imageProcessingServiceMock->expects($this->any())->method('convertToShopFormatAndGetNewFilename')->willReturn($filename);

		$imageService = new ImageService($imageProcessingServiceMock, $this->getFileUpload());
		$image = $imageService->editImageOrCreateNew($imageEntityConfig, 1, $filename, 'type', $oldImage);
		$temporaryFiles = $image->getTemporaryFilesForUpload();

		$this->assertEquals($oldImage, $image);
		$this->assertEquals($filename, array_pop($temporaryFiles)->getTemporaryFilename());
	}

	public function testeditImageOrCreateNew() {
		$imageEntityConfig = new ImageEntityConfig('entityName', 'entityClass', [], [], ['type' => true]);
		$filename = 'filename.jpg';

		$imageProcessingServiceMock = $this->getMockBuilder(ImageProcessingService::class)
			->disableOriginalConstructor()
			->setMethods(['convertToShopFormatAndGetNewFilename'])
			->getMock();
		$imageProcessingServiceMock->expects($this->any())->method('convertToShopFormatAndGetNewFilename')->willReturn($filename);

		$imageService = new ImageService($imageProcessingServiceMock, $this->getFileUpload());
		$image = $imageService->editImageOrCreateNew($imageEntityConfig, 1, $filename, 'type', null);
		$temporaryFiles = $image->getTemporaryFilesForUpload();

		$this->assertInstanceOf(Image::class, $image);
		$this->assertEquals($filename, array_pop($temporaryFiles)->getTemporaryFilename());
	}

	/**
	 * @return \SS6\ShopBundle\Model\FileUpload\FileUpload
	 */
	private function getFileUpload() {
		$fileNamingConvention = new FileNamingConvention();
		$filesystem = new Filesystem();

		return new FileUpload('temporaryDir', 'fileDir', 'imageDir', $fileNamingConvention, $filesystem);
	}

}
