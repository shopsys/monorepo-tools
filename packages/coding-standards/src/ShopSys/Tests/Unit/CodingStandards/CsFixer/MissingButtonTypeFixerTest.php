<?php

namespace ShopSys\Tests\Unit\CodingStandards\CsFixer;

use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit_Framework_TestCase;
use ShopSys\CodingStandards\CsFixer\MissingButtonTypeFixer;
use SplFileInfo;

class MissingButtonTypeFixerTest extends PHPUnit_Framework_TestCase
{
    public function testFix()
    {
        $missingButtonTypeFixer = new MissingButtonTypeFixer();

        $file = new SplFileInfo(__DIR__ . '/missingButtonTypeFixerTestcase.txt');
        $expectedResult = file_get_contents(__DIR__ . '/missingButtonTypeFixerExpectedResult.txt');
        $tokens = Tokens::fromCode(file_get_contents($file->getRealPath()));

        $missingButtonTypeFixer->fix($file, $tokens);

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
        $missingButtonTypeFixer = new MissingButtonTypeFixer();

        $splFileInfoMock = $this->mockSplFileInfoWithFilename($filename);

        $this->assertSame($expected, $missingButtonTypeFixer->supports($splFileInfoMock));
    }

    /**
     * @return array
     */
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
