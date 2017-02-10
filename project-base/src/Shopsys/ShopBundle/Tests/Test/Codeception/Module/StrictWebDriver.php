<?php

namespace Shopsys\ShopBundle\Tests\Test\Codeception\Module;

use Codeception\Module\WebDriver;
use Codeception\Util\Locator;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverElement;
use Symfony\Component\DomCrawler\Crawler;

class StrictWebDriver extends WebDriver {

    const WAIT_AFTER_CLICK_MICROSECONDS = 50000;

    /**
     * @param string[] $alternatives
     * @return string
     */
    private function getDeprecatedMethodExceptionMessage(array $alternatives) {
        $messageWithAlternativesPlaceholder = 'This method is deprecated because it uses fuzzy locators. '
            . 'Use one of strict alternatives instead: %s. Or implement new method with strict locator. See ' . self::class;

        return sprintf(
            $messageWithAlternativesPlaceholder,
            implode(', ', $alternatives)
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function match($page, $selector, $throwMalformed = true) {
        if (!is_array($selector) && !$selector instanceof WebDriverBy) {
            $message = 'Using match() with fuzzy locator is slow. '
                . 'You should implement new method with strict locator. See ' . self::class;
            throw new \Shopsys\ShopBundle\Tests\Test\Codeception\Exception\DeprecatedMethodException($message);
        }
        return parent::match($page, $selector, $throwMalformed);
    }

    /**
     * {@inheritDoc}
     */
    protected function findFields($selector) {
        if (!is_array($selector) && !$selector instanceof WebDriverElement) {
            $message = 'Using findFields() with fuzzy locator is slow. '
                . 'You should implement new method with strict locator. See ' . self::class;
            throw new \Shopsys\ShopBundle\Tests\Test\Codeception\Exception\DeprecatedMethodException($message);
        }
        return parent::findFields($selector);
    }

    /**
     * @deprecated
     */
    public function click($link, $context = null) {
        $strictAlternatives = [
            'clickBy*',
        ];
        $message = $this->getDeprecatedMethodExceptionMessage($strictAlternatives);
        throw new \Shopsys\ShopBundle\Tests\Test\Codeception\Exception\DeprecatedMethodException($message);
    }

    /**
     * @see click()
     */
    private function clickAndWait($link, $context = null) {
        parent::click($link, $context);

        // workaround for race conditions when WebDriver tries to interact with page before click was processed
        usleep(self::WAIT_AFTER_CLICK_MICROSECONDS);
    }

    /**
     * @param string $text
     * @param \Facebook\WebDriver\WebDriverBy|\Facebook\WebDriver\WebDriverElement|null $contextSelector
     */
    public function clickByText($text, $contextSelector = null) {
        $locator = Crawler::xpathLiteral(trim($text));

        $xpath = Locator::combine(
            './/a[normalize-space(.)=' . $locator . ']',
            './/button[normalize-space(.)=' . $locator . ']',
            './/a/img[normalize-space(@alt)=' . $locator . ']/ancestor::a',
            './/input[./@type = "submit" or ./@type = "image" or ./@type = "button"][normalize-space(@value)=' . $locator . ']'
        );

        $this->clickAndWait(['xpath' => $xpath], $contextSelector);
    }

    /**
     * @param string $name
     * @param \Facebook\WebDriver\WebDriverBy|\Facebook\WebDriver\WebDriverElement|null $contextSelector
     */
    public function clickByName($name, $contextSelector = null) {
        $locator = Crawler::xpathLiteral(trim($name));

        $xpath = Locator::combine(
            './/input[./@type = "submit" or ./@type = "image" or ./@type = "button"][./@name = ' . $locator . ']',
            './/button[./@name = ' . $locator . ']'
        );

        $this->clickAndWait(['xpath' => $xpath], $contextSelector);
    }

    /**
     * @param string $css
     */
    public function clickByCss($css) {
        $this->clickAndWait(['css' => $css]);
    }

    /**
     * @param \Facebook\WebDriver\WebDriverElement $element
     * @return \Facebook\WebDriver\WebDriverElement
     */
    public function clickByElement(WebDriverElement $element) {
        $element->click();
    }

    /**
     * @deprecated
     */
    public function fillField($field, $value) {
        $strictAlternatives = [
            'fillFieldBy*',
        ];
        $message = $this->getDeprecatedMethodExceptionMessage($strictAlternatives);
        throw new \Shopsys\ShopBundle\Tests\Test\Codeception\Exception\DeprecatedMethodException($message);
    }

    /**
     * @param \Facebook\WebDriver\WebDriverElement $element
     * @param string $value
     */
    public function fillFieldByElement(WebDriverElement $element, $value) {
        $element->clear();
        $element->sendKeys($value);
    }

    /**
     * @param string $fieldName
     * @param string $value
     */
    public function fillFieldByName($fieldName, $value) {
        $locator = Crawler::xpathLiteral(trim($fieldName));
        $xpath = './/*[self::input | self::textarea | self::select][@name = ' . $locator . ']';

        parent::fillField(['xpath' => $xpath], $value);
    }

    /**
     * @param string $css
     * @param string $value
     */
    public function fillFieldByCss($css, $value) {
        parent::fillField(['css' => $css], $value);
    }

    /**
     * @param string $text
     * @param string $css
     */
    public function seeInCss($text, $css) {
        parent::see($text, ['css' => $css]);
    }

    /**
     * @param string $text
     * @param \Facebook\WebDriver\WebDriverElement $element
     */
    public function seeInElement($text, WebDriverElement $element) {
        $this->assertContains($text, $element->getText());
    }

    /**
     * @deprecated
     */
    public function seeCheckboxIsChecked($checkbox) {
        $strictAlternatives = [
            'seeCheckboxIsCheckedBy*',
        ];
        $message = $this->getDeprecatedMethodExceptionMessage($strictAlternatives);
        throw new \Shopsys\ShopBundle\Tests\Test\Codeception\Exception\DeprecatedMethodException($message);
    }

    /**
     * @param string $checkboxId
     */
    public function seeCheckboxIsCheckedById($checkboxId) {
        $locator = Crawler::xpathLiteral(trim($checkboxId));
        $xpath = './/input[@type = "checkbox"][./@id = ' . $locator . ']';

        parent::seeCheckboxIsChecked(['xpath' => $xpath]);
    }

    /**
     * @param string $label
     */
    public function seeCheckboxIsCheckedByLabel($label) {

        /*
         * XPath explanation:
         *
         * First combine() argument:
         * Search for <input type="checkbox" id=myCheckboxId>,
         * where myCheckboxId is value of "for" attribute of <label for=myCheckboxId>$label</label>.
         *
         * Second combine() argument:
         * Search for <label>$label</label>. Inside of it search for <input type="checkbox">.
         */
        $xpath = Locator::combine(
            './/*[self::input[@type="checkbox"]][./@id = //label[contains(normalize-space(string(.)), "' . $label . '")]/@for]',
            './/label[contains(normalize-space(string(.)), "' . $label . '")]//.//*[self::input[@type="checkbox"]]'
        );

        parent::seeCheckboxIsChecked(['xpath' => $xpath]);
    }

    /**
     * @deprecated
     */
    public function dontSeeCheckboxIsChecked($checkbox) {
        $strictAlternatives = [
            'dontSeeCheckboxIsCheckedBy*',
        ];
        $message = $this->getDeprecatedMethodExceptionMessage($strictAlternatives);
        throw new \Shopsys\ShopBundle\Tests\Test\Codeception\Exception\DeprecatedMethodException($message);
    }

    /**
     * @param string $checkboxId
     */
    public function dontSeeCheckboxIsCheckedById($checkboxId) {
        $locator = Crawler::xpathLiteral(trim($checkboxId));
        $xpath = './/input[@type = "checkbox"][./@id = ' . $locator . ']';

        parent::dontSeeCheckboxIsChecked(['xpath' => $xpath]);
    }

    /**
     * @param string $label
     */
    public function dontSeeCheckboxIsCheckedByLabel($label) {

        /*
         * XPath explanation:
         *
         * First combine() argument:
         * Search for <input type="checkbox" id=myCheckboxId>,
         * where myCheckboxId is value of "for" attribute of <label for=myCheckboxId>$label</label>.
         *
         * Second combine() argument:
         * Search for <label>$label</label>. Inside of it search for <input type="checkbox">.
         */
        $xpath = Locator::combine(
            './/*[self::input[@type="checkbox"]][./@id = //label[contains(normalize-space(string(.)), "' . $label . '")]/@for]',
            './/label[contains(normalize-space(string(.)), "' . $label . '")]//.//*[self::input[@type="checkbox"]]'
        );

        parent::dontSeeCheckboxIsChecked(['xpath' => $xpath]);
    }

    /**
     * @deprecated
     */
    public function checkOption($option) {
        $strictAlternatives = [
            'checkOptionBy*',
        ];
        $message = $this->getDeprecatedMethodExceptionMessage($strictAlternatives);
        throw new \Shopsys\ShopBundle\Tests\Test\Codeception\Exception\DeprecatedMethodException($message);
    }

    /**
     * @param string $optionId
     */
    public function checkOptionById($optionId) {
        $locator = Crawler::xpathLiteral(trim($optionId));
        $xpath = './/input[@type = "checkbox" or @type = "radio"][./@id = ' . $locator . ']';

        parent::checkOption(['xpath' => $xpath]);
    }

    /**
     * @param string $label
     */
    public function checkOptionByLabel($label) {

        /*
         * XPath explanation:
         *
         * First combine() argument:
         * Search for <input type="checkbox" id=myCheckboxId>,
         * where myCheckboxId is value of "for" attribute of <label for=myCheckboxId>$label</label>.
         *
         * Second combine() argument:
         * Search for <label>$label</label>. Inside of it search for <input type="checkbox">.
         */
        $xpath = Locator::combine(
            './/*[self::input[@type="checkbox"]][./@id = //label[contains(normalize-space(string(.)), "' . $label . '")]/@for]',
            './/label[contains(normalize-space(string(.)), "' . $label . '")]//.//*[self::input[@type="checkbox"]]'
        );
        parent::checkOption(['xpath' => $xpath]);
    }

    /**
     * @param string $selectCss
     * @param string $optionValue
     */
    public function selectOptionByCssAndValue($selectCss, $optionValue) {
        parent::selectOption(['css' => $selectCss], $optionValue);
    }

    /**
     * @param string $css
     * @return int
     */
    public function countVisibleByCss($css) {
        $elements = parent::matchVisible(['css' => $css]);

        return count($elements);
    }

    /**
     * @deprecated
     */
    public function seeInField($field, $value) {
        $strictAlternatives = [
            'seeInFieldBy*',
        ];
        $message = $this->getDeprecatedMethodExceptionMessage($strictAlternatives);
        throw new \Shopsys\ShopBundle\Tests\Test\Codeception\Exception\DeprecatedMethodException($message);
    }

    /**
     * @param string $value
     * @param string $fieldName
     */
    public function seeInFieldByName($value, $fieldName) {
        $locator = Crawler::xpathLiteral(trim($fieldName));
        $xpath = './/*[self::input | self::textarea | self::select][@name = ' . $locator . ']';

        parent::seeInField(['xpath' => $xpath], $value);
    }

    /**
     * @param string $value
     * @param \Facebook\WebDriver\WebDriverElement $element
     */
    public function seeInFieldByElement($value, WebDriverElement $element) {
        parent::seeInField($element, $value);
    }

    /**
     * @param string $css
     * @param null|int $offsetX
     * @param null|int $offsetY
     */
    public function moveMouseOverByCss($css, $offsetX = null, $offsetY = null) {
        parent::moveMouseOver(['css' => $css], $offsetX, $offsetY);
    }

    /**
     * @deprecated
     */
    public function pressKey($element, $char) {
        $strictAlternatives = [
            'pressKeysBy*',
        ];
        $message = $this->getDeprecatedMethodExceptionMessage($strictAlternatives);
        throw new \Shopsys\ShopBundle\Tests\Test\Codeception\Exception\DeprecatedMethodException($message);
    }

    /**
     * Examples:
     * $I->pressKeysByElement($element, 'hello'); // hello
     * $I->pressKeysByElement($element, ['n', 'e', 'w']); // new
     * $I->pressKeysByElement($element, [[\Facebook\WebDriver\WebDriverKeys, 'day'], 1]); // DAY1
     *
     * For available keys:
     * @see \Facebook\WebDriver\WebDriverKeys
     *
     * @param \Facebook\WebDriver\WebDriverElement $element
     * @param string|string[] $keys
     */
    public function pressKeysByElement(WebDriverElement $element, $keys) {
        $element->sendKeys($keys);
    }

}
