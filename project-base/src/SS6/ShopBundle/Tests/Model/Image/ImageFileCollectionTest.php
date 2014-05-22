<?php

namespace SS6\ShopBundle\Tests\Model\Image;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Image\Exception\TypeNotExistException;
use SS6\ShopBundle\Model\Image\ImageFile;
use SS6\ShopBundle\Model\Image\ImageFileCollection;

class ImageFileCollectionTest extends PHPUnit_Framework_TestCase {
	public function testGetImageFile() {
		$imageFileCollection = new ImageFileCollection('categoryName');
		$imageFileCollection->addImageFile('filename1.png');
		$imageFileCollection->addImageFile('filename2.png', '', 'type');

		$noTypeImageFile = $imageFileCollection->getImageFile();
		$imageFile = $imageFileCollection->getImageFile('type');

		$this->assertInstanceOf(ImageFile::class, $noTypeImageFile);
		$this->assertInstanceOf(ImageFile::class, $imageFile);
	}

	public function testGetImageFileUnknowType() {
		$imageFileCollection = new ImageFileCollection('categoryName');
		$imageFileCollection->addImageFile('filename1.png');
		$imageFileCollection->addImageFile('filename2.png', '', 'type');

		$this->setExpectedException(TypeNotExistException::class);
		$imageFileCollection->getImageFile('unknowType');
	}
}
