<?php

namespace Shopsys\Tests\Unit\CodingStandards\CsFixer;

use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase;
use Shopsys\CodingStandards\CsFixer\MissingButtonTypeFixer;
use SplFileInfo;

class MissingButtonTypeFixerTest extends TestCase
{
    public function testFix(): void
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
     */
    public function testSupports(string $filename, bool $expected): void
    {
        $missingButtonTypeFixer = new MissingButtonTypeFixer();

        $splFileInfoMock = $this->mockSplFileInfoWithFilename($filename);

        $this->assertSame($expected, $missingButtonTypeFixer->supports($splFileInfoMock));
    }

    public function supportsDataProvider(): array
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
     * @return \PHPUnit\Framework\MockObject\MockObject|\SplFileInfo
     */
    private function mockSplFileInfoWithFilename(string $filename)
    {
        $splFileInfoMock = $this->getMockBuilder(SplFileInfo::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFilename'])
            ->getMock();
        $splFileInfoMock->expects($this->any())->method('getFilename')->willReturn($filename);

        return $splFileInfoMock;
    }
}
