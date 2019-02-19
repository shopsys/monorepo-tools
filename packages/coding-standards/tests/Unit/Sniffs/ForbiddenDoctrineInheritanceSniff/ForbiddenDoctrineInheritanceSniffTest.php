<?php

declare(strict_types=1);

namespace Tests\CodingStandards\Sniffs\ForbiddenExitSniff;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class ForbiddenDoctrineInheritanceSniffTest extends AbstractCheckerTestCase
{
    public function testWrong(): void
    {
        $this->doTestWrongFile(__DIR__ . '/Wrong/ClassWithFullNamespaceInheritanceMapping.php');
        $this->doTestWrongFile(__DIR__ . '/Wrong/EntityWithOrmInheritanceMapping.php');
    }

    public function testCorrect(): void
    {
        $this->doTestCorrectFile(__DIR__ . '/Correct/fileWithoutClass.php');
        $this->doTestCorrectFile(__DIR__ . '/Correct/EntityWithoutInheritanceMapping.php');
    }

    /**
     * @return string
     */
    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
