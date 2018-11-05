<?php

declare(strict_types=1);

namespace Tests\CodingStandards\Unit\CsFixer\Phpdoc\MissingParamAnnotationsFixer;

use Iterator;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

/**
 * @covers \Shopsys\CodingStandards\CsFixer\Phpdoc\MissingParamAnnotationsFixer
 */
final class MissingParamAnnotationsFixer extends AbstractCheckerTestCase
{
    /**
     * @dataProvider provideWrongToFixedFiles()
     * @param string $wrongFile
     * @param string $fixedFile
     */
    public function testFix(string $wrongFile, string $fixedFile): void
    {
        $this->doTestWrongToFixedFile($wrongFile, $fixedFile);
    }

    /**
     * @return \Iterator
     */
    public function provideWrongToFixedFiles(): Iterator
    {
        yield [__DIR__ . '/wrong/wrong.php', __DIR__ . '/fixed/fixed.php'];
        yield [__DIR__ . '/wrong/wrong2.php', __DIR__ . '/fixed/fixed2.php'];
        yield [__DIR__ . '/wrong/wrong3.php', __DIR__ . '/fixed/fixed3.php'];
        yield [__DIR__ . '/wrong/wrong4.php', __DIR__ . '/fixed/fixed4.php'];
        yield [__DIR__ . '/wrong/wrong5.php', __DIR__ . '/fixed/fixed5.php'];
        yield [__DIR__ . '/wrong/wrong6.php', __DIR__ . '/fixed/fixed6.php'];
    }

    /**
     * @dataProvider provideCorrectFiles()
     * @param string $correctFile
     */
    public function testCorrect(string $correctFile): void
    {
        $this->doTestCorrectFile($correctFile);
    }

    /**
     * @return \Iterator
     */
    public function provideCorrectFiles(): Iterator
    {
        yield [__DIR__ . '/correct/correct.php'];
    }

    /**
     * @return string
     */
    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
