<?php

declare(strict_types=1);

namespace Tests\CodingStandards\Sniffs\ObjectIsCreatedByFactorySniff;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class ObjectIsCreatedByFactorySniffTest extends AbstractCheckerTestCase
{
    public function testCorrect(): void
    {
        $this->doTestCorrectFile(__DIR__ . '/Correct/PostFactory.php');
    }

    public function testWrong(): void
    {
        require_once __DIR__ . '/Wrong/SomeController.php';
        require_once __DIR__ . '/Wrong/PostFactory.php';

        $this->doTestWrongFile(__DIR__ . '/Wrong/SomeController.php');
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
