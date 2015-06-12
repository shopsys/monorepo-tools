<?php

namespace SS6\ShopBundle\Tests\Unit\Component\CsFixer;

use PHPUnit_Framework_TestCase;
use SplFileInfo;
use SS6\ShopBundle\Component\CsFixer\MissingButtonTypeFixer;

class MissingButtonTypeFixerTest extends PHPUnit_Framework_TestCase {

	public function testFix() {
		$file = new SplFileInfo(__DIR__ . '/testcase.txt');
		$expectedResult = file_get_contents(__DIR__ . '/expectedResult.txt');

		$missingButtonTypeFixer = new MissingButtonTypeFixer();
		$result = $missingButtonTypeFixer->fix($file, file_get_contents($file->getRealPath()));

		$this->assertSame($expectedResult, $result);
	}

	/**
	 * @dataProvider testSupportsDataProvider
	 * @param string $filename
	 * @param bool $expected
	 */
	public function testSupports($filename, $expected) {
		$missingButtonTypeFixer = new MissingButtonTypeFixer();

		$splFileInfoMock = $this->getMockBuilder(SplFileInfo::class)
			->disableOriginalConstructor()
			->setMethods(['getFilename'])
			->getMock();
		$splFileInfoMock->expects($this->any())->method('getFilename')->willReturn($filename);

		$this->assertSame($expected, $missingButtonTypeFixer->supports($splFileInfoMock));
	}

	public function testSupportsDataProvider() {
		return [
			['test.php', false],
			['test.html', true],
			['test.twig', false],
			['test.php.twig', false],
			['test.html.twig', true],
			['test.html.xxx', false],
		];
	}

}
