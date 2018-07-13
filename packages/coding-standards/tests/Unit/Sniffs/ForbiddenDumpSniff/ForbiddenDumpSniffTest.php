<?php

declare(strict_types=1);

namespace Tests\CodingStandards\Sniffs\ForbiddenDumpSniff;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class ForbiddenDumpSniffTest extends AbstractCheckerTestCase
{
    public function testWrong(): void
    {
        $this->doTestWrongFile(__DIR__ . '/wrong/d.php.inc');
        $this->doTestWrongFile(__DIR__ . '/wrong/dump.php.inc');
        $this->doTestWrongFile(__DIR__ . '/wrong/print_r.php.inc');
        $this->doTestWrongFile(__DIR__ . '/wrong/var_dump.php.inc');
        $this->doTestWrongFile(__DIR__ . '/wrong/var_export.php.inc');
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
