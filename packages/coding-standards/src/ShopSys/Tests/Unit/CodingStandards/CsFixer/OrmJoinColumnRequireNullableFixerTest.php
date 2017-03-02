<?php

namespace ShopSys\Tests\Unit\CodingStandards\CsFixer;

use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit_Framework_TestCase;
use ShopSys\CodingStandards\CsFixer\OrmJoinColumnRequireNullableFixer;
use SplFileInfo;

class OrmJoinColumnRequireNullableFixerTest extends PHPUnit_Framework_TestCase
{
    public function testFix()
    {
        $ormJoinColumnRequireNullableFixer = new OrmJoinColumnRequireNullableFixer();

        $file = new SplFileInfo(__DIR__ . '/ormJoinColumnRequireNullableFixerTestcase.txt');
        $expectedResult = file_get_contents(__DIR__ . '/ormJoinColumnRequireNullableFixerExpectedResult.txt');
        $tokens = Tokens::fromCode(file_get_contents($file->getRealPath()));

        $ormJoinColumnRequireNullableFixer->fix($file, $tokens);

        $this->assertSame($expectedResult, $tokens->generateCode());
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

        $splFileInfoMock = $this->mockSplFileInfoWithFilename($filename);

        $this->assertSame($expected, $ormJoinColumnRequireNullableFixer->supports($splFileInfoMock));
    }

    /**
     * @return array
     */
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

    /**
     * @param $filename
     * @return \PHPUnit_Framework_MockObject_MockObject|\SplFileInfo
     */
    private function mockSplFileInfoWithFilename($filename)
    {
        $splFileInfoMock = $this->getMockBuilder(SplFileInfo::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFilename'])
            ->getMock();
        $splFileInfoMock->expects($this->any())->method('getFilename')->willReturn($filename);

        return $splFileInfoMock;
    }
}
