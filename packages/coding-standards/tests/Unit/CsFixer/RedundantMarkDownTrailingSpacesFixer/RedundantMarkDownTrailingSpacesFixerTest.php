<?php

declare(strict_types=1);

namespace Tests\CodingStandards\CsFixer\RedundantMarkDownTrailingSpacesFixer;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class RedundantMarkDownTrailingSpacesFixerTest extends AbstractCheckerTestCase
{
    public function testFix(): void
    {
        $this->doTestWrongToFixedFile(__DIR__ . '/wrong/wrong.md', __DIR__ . '/fixed/fixed.md');
    }

    public function testCorrect(): void
    {
        $this->doTestCorrectFile(__DIR__ . '/correct/correct.md');
    }

    /**
     * @return string
     */
    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
