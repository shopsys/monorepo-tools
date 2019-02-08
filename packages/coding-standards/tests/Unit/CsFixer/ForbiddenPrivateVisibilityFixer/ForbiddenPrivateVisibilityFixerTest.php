<?php

declare(strict_types=1);

namespace Tests\CodingStandards\CsFixer\ForbiddenDumpFixer;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class ForbiddenPrivateVisibilityFixerTest extends AbstractCheckerTestCase
{
    public function testFix(): void
    {
        $this->doTestWrongToFixedFile(__DIR__ . '/wrong/wrong.php', __DIR__ . '/fixed/fixed.php');
    }

    public function testCorrect(): void
    {
        $this->doTestCorrectFile(__DIR__ . '/correct/correct.php');
        $this->doTestCorrectFile(__DIR__ . '/correct/ignored-namespace.php');
    }

    /**
     * @return string
     */
    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
