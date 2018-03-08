<?php

namespace ShopSys\Tests\Unit\CodingStandards\CsFixer;

use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase;
use ShopSys\CodingStandards\CsFixer\OrmJoinColumnRequireNullableFixer;
use SplFileInfo;

class OrmJoinColumnRequireNullableFixerTest extends TestCase
{
    public function testFix(): void
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
     */
    public function testSupports(string $filename, bool $expected): void
    {
        $ormJoinColumnRequireNullableFixer = new OrmJoinColumnRequireNullableFixer();

        $splFileInfoMock = $this->mockSplFileInfoWithFilename($filename);

        $this->assertSame($expected, $ormJoinColumnRequireNullableFixer->supports($splFileInfoMock));
    }

    public function supportsDataProvider(): array
    {
        return [
            ['test.php', true],
            ['test.html', false],
            ['test.twig', false],
            ['test.php.twig', false],
            ['test.html.twig', false],
            ['test.php.xxx', false],
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
