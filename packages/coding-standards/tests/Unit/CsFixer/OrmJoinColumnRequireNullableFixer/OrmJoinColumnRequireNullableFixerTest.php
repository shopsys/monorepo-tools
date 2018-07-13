<?php

declare(strict_types=1);

namespace Tests\CodingStandards\CsFixer\OrmJoinColumnRequireNullableFixer;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class OrmJoinColumnRequireNullableFixerTest extends AbstractCheckerTestCase
{
    public function testFix(): void
    {
        $this->doTestWrongToFixedFile(__DIR__ . '/wrong/many_to_one_missing_join_column.php', __DIR__ . '/fixed/many_to_one_missing_join_column.php');
        $this->doTestWrongToFixedFile(__DIR__ . '/wrong/many_to_one_missing_nullable_param.php', __DIR__ . '/fixed/many_to_one_missing_nullable_param.php');
        $this->doTestWrongToFixedFile(__DIR__ . '/wrong/one_to_one_missing_join_column.php', __DIR__ . '/fixed/one_to_one_missing_join_column.php');
        $this->doTestWrongToFixedFile(__DIR__ . '/wrong/one_to_one_missing_nullable_param.php', __DIR__ . '/fixed/one_to_one_missing_nullable_param.php');
        $this->doTestWrongToFixedFile(__DIR__ . '/wrong/one_to_one_multiline_missing_nullable_param.php', __DIR__ . '/fixed/one_to_one_multiline_missing_nullable_param.php');
    }

    public function testCorrect(): void
    {
        $this->doTestCorrectFile(__DIR__ . '/correct/one_to_many.php');
        $this->doTestCorrectFile(__DIR__ . '/correct/many_to_one_missing_join_column.php');
        $this->doTestCorrectFile(__DIR__ . '/correct/many_to_one_missing_nullable_param.php');
        $this->doTestCorrectFile(__DIR__ . '/correct/one_to_one_missing_nullable_param.php');
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
