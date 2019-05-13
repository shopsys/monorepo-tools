<?php

declare(strict_types=1);

namespace Tests\CodingStandards\Sniffs\ConstantVisibilityRequiredSniff;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class ConstantVisibilityRequiredSniffTest extends AbstractCheckerTestCase
{
    public function testCorrect(): void
    {
        $this->doTestCorrectFile(__DIR__ . '/correct/Annotation.php');
        $this->doTestCorrectFile(__DIR__ . '/correct/SingleValueWithoutNamespace.php');
        $this->doTestCorrectFile(__DIR__ . '/correct/SingleValueAfterMethodWithoutNamespace.php');
        $this->doTestCorrectFile(__DIR__ . '/correct/MultipleValues.php');
        $this->doTestCorrectFile(__DIR__ . '/correct/Mixed.php');
        $this->doTestCorrectFile(__DIR__ . '/correct/MixedVisibilities.php');
        $this->doTestCorrectFile(__DIR__ . '/correct/noClass.php');
        $this->doTestCorrectFile(__DIR__ . '/correct/OutsideClass.php');
    }

    public function testWrong(): void
    {
        $this->doTestWrongFile(__DIR__ . '/wrong/SingleValue.php');
        $this->doTestWrongFile(__DIR__ . '/wrong/MissingAnnotation.php');
        $this->doTestWrongFile(__DIR__ . '/wrong/Mixed.php');
        $this->doTestWrongFile(__DIR__ . '/wrong/MixedAtTheEnd.php');
        $this->doTestWrongFile(__DIR__ . '/wrong/MixedInTheMiddle.php');
        $this->doTestWrongFile(__DIR__ . '/wrong/SingleValueAfterMethodWithoutNamespace.php');
    }

    /**
     * @return string
     */
    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
