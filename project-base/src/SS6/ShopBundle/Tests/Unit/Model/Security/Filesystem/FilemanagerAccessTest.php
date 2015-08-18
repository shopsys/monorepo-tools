<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Security\Filesystem;

use FM\ElfinderBundle\Configuration\ElFinderConfigurationReader;
use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Security\Filesystem\FilemanagerAccess;

class FilemanagerAccessTest extends PHPUnit_Framework_TestCase {

	public function isPathAccessibleProvider() {
		return [
			[
				__DIR__,
				__DIR__,
				'read',
				null,
			],
			[
				__DIR__,
				__DIR__ . '/foo',
				'read',
				null,
			],
			[
				__DIR__,
				__DIR__ . 'foo',
				'read',
				false,
			],
			[
				__DIR__,
				__DIR__ . '/.foo',
				'read',
				false,
			],
		];
	}

	/**
	 * @dataProvider isPathAccessibleProvider
	 */
	public function testIsPathAccessible($fileuploadDir, $testPath, $attr, $isAccessible) {
		$elFinderConfigurationReaderMock = $this->getMock(ElFinderConfigurationReader::class, null, [], '', false);
		$filemanagerAccess = new FilemanagerAccess($fileuploadDir, $elFinderConfigurationReaderMock);

		$this->assertSame($filemanagerAccess->isPathAccessible($attr, $testPath, null, null), $isAccessible);
	}

	/**
	 * @dataProvider isPathAccessibleProvider
	 */
	public function testIsPathAccessibleStatic($fileuploadDir, $testPath, $attr, $isAccessible) {
		$elFinderConfigurationReaderMock = $this->getMock(ElFinderConfigurationReader::class, null, [], '', false);
		$filemanagerAccess = new FilemanagerAccess($fileuploadDir, $elFinderConfigurationReaderMock);
		FilemanagerAccess::injectSelf($filemanagerAccess);

		$this->assertSame(FilemanagerAccess::isPathAccessibleStatic($attr, $testPath, null, null), $isAccessible);
	}

	public function testIsPathAccessibleStaticException() {
		FilemanagerAccess::detachSelf();
		$this->setExpectedException(\SS6\ShopBundle\Model\Security\Filesystem\Exception\InstanceNotInjectedException::class);
		FilemanagerAccess::isPathAccessibleStatic('read', __DIR__, null, null);
	}
}
