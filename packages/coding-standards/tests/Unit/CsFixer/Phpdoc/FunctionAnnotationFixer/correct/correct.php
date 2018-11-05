<?php

declare(strict_types=1);

namespace Tests\CodingStandards\Unit\CsFixer\Phpdoc\MissingReturnAnnotationFixer;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

/**
 * @covers \Shopsys\CodingStandards\CsFixer\Phpdoc\MissingReturnAnnotationFixer
 */
final class MissingReturnAnnotationFixerTest extends AbstractCheckerTestCase
{
    /**
     * $I->pressKeysByElement($element, [[\Facebook\WebDriver\WebDriverKeys, 'day'], 1]); // DAY1
     *
     * @param \Facebook\WebDriver\WebDriverElement $element
     * @param string|string[] $keys
     */
    public function pressKeysByElement(WebDriverElement $element, $keys)
    {
    }
}
