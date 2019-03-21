<?php

declare(strict_types=1);

namespace Tests\CodingStandards\CsFixer\OrmJoinColumnRequireNullableFixer;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class NoUselessAccessFixerTest extends AbstractCheckerTestCase
{
    public function testFix(): void
    {
        $this->doTestWrongToFixedFile(__DIR__ . '/wrong/Simple.php', __DIR__ . '/fixed/Simple.php');
        $this->doTestWrongToFixedFile(__DIR__ . '/wrong/Complex.php', __DIR__ . '/fixed/Complex.php');
        $this->doTestWrongToFixedFile(__DIR__ . '/wrong/DifferentAnnotationAndVisibility.php', __DIR__ . '/fixed/DifferentAnnotationAndVisibility.php');
        $this->doTestWrongToFixedFile(__DIR__ . '/wrong/EmptyAccessAnnotation.php', __DIR__ . '/fixed/EmptyAccessAnnotation.php');
    }

    public function testCorrect(): void
    {
        $this->doTestCorrectFile(__DIR__ . '/fixed/Simple.php');
        $this->doTestCorrectFile(__DIR__ . '/fixed/Complex.php');
    }

    /**
     * @return string
     */
    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
