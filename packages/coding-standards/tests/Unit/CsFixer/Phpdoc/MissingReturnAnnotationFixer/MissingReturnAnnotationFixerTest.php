<?php

declare(strict_types=1);

namespace Tests\CodingStandards\Unit\CsFixer\Phpdoc\MissingReturnAnnotationFixer;

use Iterator;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

/**
 * @covers \Shopsys\CodingStandards\CsFixer\Phpdoc\MissingReturnAnnotationFixer
 */
final class MissingReturnAnnotationFixerTest extends AbstractCheckerTestCase
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
