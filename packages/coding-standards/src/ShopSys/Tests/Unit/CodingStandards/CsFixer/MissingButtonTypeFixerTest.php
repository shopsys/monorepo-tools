<?php

namespace ShopSys\Tests\Unit\CodingStandards\CsFixer;

use PHPUnit_Framework_TestCase;
use ShopSys\CodingStandards\CsFixer\MissingButtonTypeFixer;
use SplFileInfo;

class MissingButtonTypeFixerTest extends PHPUnit_Framework_TestCase
{
    public function testFix()
    {
        $file = new SplFileInfo(__DIR__ . '/missingButtonTypeFixerTestcase.txt');
        $expectedResult = file_get_contents(__DIR__ . '/missingButtonTypeFixerExpectedResult.txt');

        $missingButtonTypeFixer = new MissingButtonTypeFixer();
        $result = $missingButtonTypeFixer->fix($file, file_get_contents($file->getRealPath()));

        $this->assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider supportsDataProvider
     *
     * @param string $filename
     * @param bool   $expected
     */
    public function testSupports($filename, $expected)
    {
        $missingButtonTypeFixer = new MissingButtonTypeFixer();

        $splFileInfoMock = $this->getMockBuilder(SplFileInfo::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFilename'])
            ->getMock();
        $splFileInfoMock->expects($this->any())->method('getFilename')->willReturn($filename);

        $this->assertSame($expected, $missingButtonTypeFixer->supports($splFileInfoMock));
    }

    public function supportsDataProvider()
    {
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
