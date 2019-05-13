<?php

declare(strict_types=1);

namespace Tests\CodingStandards\Sniffs\ForceLateStaticBindingForProtectedConstantsSniff;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class ForceLateStaticBindingForProtectedConstantsSniffTest extends AbstractCheckerTestCase
{
    public function testFix(): void
    {
        $this->doTestWrongToFixedFile(__DIR__ . '/wrong/SingleValue.php', __DIR__ . '/fixed/SingleValue.php');
        $this->doTestWrongToFixedFile(__DIR__ . '/wrong/SelfWithMethodsAndVariables.php', __DIR__ . '/fixed/SelfWithMethodsAndVariables.php');
    }

    /**
     * @return string
     */
    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
