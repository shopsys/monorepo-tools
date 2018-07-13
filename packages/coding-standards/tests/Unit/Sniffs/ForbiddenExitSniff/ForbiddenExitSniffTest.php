<?php

declare(strict_types=1);

namespace Tests\CodingStandards\Sniffs\ForbiddenExitSniff;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class ForbiddenExitSniffTest extends AbstractCheckerTestCase
{
    public function testWrong(): void
    {
        $this->doTestWrongFile(__DIR__ . '/wrong/wrong.php.inc');
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
