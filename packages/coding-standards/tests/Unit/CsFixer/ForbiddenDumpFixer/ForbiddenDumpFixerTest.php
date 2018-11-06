<?php

declare(strict_types=1);

namespace Tests\CodingStandards\CsFixer\ForbiddenDumpFixer;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class ForbiddenDumpFixerTest extends AbstractCheckerTestCase
{
    public function testFix(): void
    {
        $this->doTestWrongToFixedFile(__DIR__ . '/wrong/wrong.html.twig', __DIR__ . '/fixed/fixed.html.twig');
    }

    public function testCorrect(): void
    {
        $this->doTestCorrectFile(__DIR__ . '/correct/correct.html.twig');
    }

    /**
     * @return string
     */
    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
