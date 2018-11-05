<?php

declare(strict_types=1);

namespace Tests\CodingStandards\Unit\CsFixer\Phpdoc\OrderedParamAnnotationsFixer;

use Iterator;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

/**
 * @covers \Shopsys\CodingStandards\CsFixer\Phpdoc\OrderedParamAnnotationsFixer
 */
final class OrderedParamAnnotationsFixerTest extends AbstractCheckerTestCase
{
    /**
     * @dataProvider provideWrongToFixedFiles()
     */
    public function testFix(string $wrongFile, string $fixedFile): void
    {
        $this->doTestWrongToFixedFile($wrongFile, $fixedFile);
    }

    public function provideWrongToFixedFiles(): Iterator
    {
        yield [__DIR__ . '/wrong/wrong.php', __DIR__ . '/fixed/fixed.php'];
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
        yield [__DIR__ . '/correct/correct2.php'];
        yield [__DIR__ . '/correct/correct3.php'];
        yield [__DIR__ . '/correct/correct4.php'];
    }

    /**
     * @return string
     */
    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
