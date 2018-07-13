<?php

declare(strict_types=1);

namespace Tests\CodingStandards\Sniffs\ValidVariableNameSniff;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class ValidVariableNameSniffTest extends AbstractCheckerTestCase
{
    public function testWrong(): void
    {
        $this->doTestWrongFile(__DIR__ . '/wrong/wrong.inc');
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
