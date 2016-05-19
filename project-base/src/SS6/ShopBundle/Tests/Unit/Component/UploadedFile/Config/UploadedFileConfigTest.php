<?php

namespace SS6\ShopBundle\Tests\Unit\Component\UploadedFile;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Component\UploadedFile\Config\UploadedFileConfig;
use SS6\ShopBundle\Component\UploadedFile\Config\UploadedFileEntityConfig;
use SS6\ShopBundle\Tests\Unit\Component\UploadedFile\Dummy;

/**
 * @UglyTest
 */
class UploadedFileConfigTest extends PHPUnit_Framework_TestCase {

	public function testGetEntityName() {
		$entity = new Dummy();
		$fileEntityConfigsByClass = [
			Dummy::class => new UploadedFileEntityConfig('entityName', Dummy::class),
		];
		$uploadedFileConfig = new UploadedFileConfig($fileEntityConfigsByClass);

		$this->assertSame('entityName', $uploadedFileConfig->getEntityName($entity));
	}

	public function testGetEntityNameNotFoundException() {
		$entity = new Dummy();
		$fileEntityConfigsByClass = [];
		$uploadedFileConfig = new UploadedFileConfig($fileEntityConfigsByClass);

		$this->setExpectedException(
			\SS6\ShopBundle\Component\UploadedFile\Config\Exception\UploadedFileEntityConfigNotFoundException::class
		);
		$uploadedFileConfig->getEntityName($entity);
	}

	public function testGetAllUploadedFileEntityConfigs() {
		$fileEntityConfigsByClass = [
			Dummy::class => new UploadedFileEntityConfig('entityName', Dummy::class),
		];
		$uploadedFileConfig = new UploadedFileConfig($fileEntityConfigsByClass);

		$this->assertSame($fileEntityConfigsByClass, $uploadedFileConfig->getAllUploadedFileEntityConfigs());
	}

	public function testGetUploadedFileEntityConfig() {
		$entity = new Dummy();
		$fileEntityConfigsByClass = [];
		$uploadedFileConfig = new UploadedFileConfig($fileEntityConfigsByClass);

		$this->setExpectedException(
			\SS6\ShopBundle\Component\UploadedFile\Config\Exception\UploadedFileEntityConfigNotFoundException::class
		);
		$uploadedFileConfig->getUploadedFileEntityConfig($entity);
	}

	public function testHasUploadedFileEntityConfig() {
		$entity = new Dummy();
		$fileEntityConfigsByClass = [
			Dummy::class => new UploadedFileEntityConfig('entityName', Dummy::class),
		];
		$uploadedFileConfig = new UploadedFileConfig($fileEntityConfigsByClass);

		$this->assertTrue($uploadedFileConfig->hasUploadedFileEntityConfig($entity));
	}

	public function testHasNotUploadedFileEntityConfig() {
		$entity = new Dummy();
		$fileEntityConfigsByClass = [];
		$uploadedFileConfig = new UploadedFileConfig($fileEntityConfigsByClass);

		$this->assertFalse($uploadedFileConfig->hasUploadedFileEntityConfig($entity));
	}

}
