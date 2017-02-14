<?php

namespace ShopSys\Tests\Unit\CodingStandards\CsFixer;

use PHPUnit_Framework_TestCase;
use ShopSys\CodingStandards\CsFixer\OrmJoinColumnRequireNullableFixer;
use SplFileInfo;

class OrmJoinColumnRequireNullableFixerTest extends PHPUnit_Framework_TestCase
{
    public function testFix()
    {
        $file = new SplFileInfo(__DIR__ . '/ormJoinColumnRequireNullableFixerTestcase.txt');
        $expectedResult = file_get_contents(__DIR__ . '/ormJoinColumnRequireNullableFixerExpectedResult.txt');

        $ormJoinColumnRequireNullableFixer = new OrmJoinColumnRequireNullableFixer();
        $result = $ormJoinColumnRequireNullableFixer->fix($file, file_get_contents($file->getRealPath()));

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
        $ormJoinColumnRequireNullableFixer = new OrmJoinColumnRequireNullableFixer();

        $splFileInfoMock = $this->getMockBuilder(SplFileInfo::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFilename'])
            ->getMock();
        $splFileInfoMock->expects($this->any())->method('getFilename')->willReturn($filename);

        $this->assertSame($expected, $ormJoinColumnRequireNullableFixer->supports($splFileInfoMock));
    }

    public function supportsDataProvider()
    {
        return [
            ['test.php', true],
            ['test.html', false],
            ['test.twig', false],
            ['test.php.twig', true],
            ['test.html.twig', false],
            ['test.php.xxx', false],
        ];
    }
}
