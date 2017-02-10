<?php

namespace Shopsys\ShopBundle\Tests\Acceptance\acceptance\PageObject\Front;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;
use Shopsys\ShopBundle\Tests\Acceptance\acceptance\PageObject\AbstractPage;
use Shopsys\ShopBundle\Tests\Test\Codeception\AcceptanceTester;
use Shopsys\ShopBundle\Tests\Test\Codeception\Module\StrictWebDriver;

class ProductFilterPage extends AbstractPage
{

    // Product filter waits for more requests before evaluation
    const PRE_EVALUATION_WAIT = 2;

    public function __construct(StrictWebDriver $strictWebDriver, AcceptanceTester $tester) {
        parent::__construct($strictWebDriver, $tester);
    }

    /**
     * @param string $price
     */
    public function setMinimalPrice($price) {
        $this->tester->fillFieldByCss('#product_filter_form_minimalPrice', $price . WebDriverKeys::ENTER);
        $this->waitForFilter();
    }

    /**
     * @param string $price
     */
    public function setMaximalPrice($price) {
        $this->tester->fillFieldByCss('#product_filter_form_maximalPrice', $price . WebDriverKeys::ENTER);
        $this->waitForFilter();
    }

    /**
     * @param string $label
     */
    public function filterByBrand($label) {
        $this->tester->checkOptionByLabel($label);
        $this->waitForFilter();
    }

    /**
     * @param string $parameterLabel
     * @param string $valueLabel
     */
    public function filterByParameter($parameterLabel, $valueLabel) {
        $parameterElement = $this->findParameterElementByLabel($parameterLabel);
        $labelElement = $this->getLabelElementByParameterValueText($parameterElement, $valueLabel);
        $labelElement->click();
        $this->waitForFilter();
    }

    private function waitForFilter() {
        $this->tester->wait(self::PRE_EVALUATION_WAIT);
        $this->tester->waitForAjax();
    }

    /**
     * @param string $parameterLabel
     * @return \Facebook\WebDriver\WebDriverElement
     */
    private function findParameterElementByLabel($parameterLabel) {
        $parameterItems = $this->webDriver->findElements(
            WebDriverBy::cssSelector('#product_filter_form_parameters .js-product-filter-parameter')
        );

        foreach ($parameterItems as $item) {
            try {
                $itemLabel = $item->findElement(WebDriverBy::cssSelector('.js-product-filter-parameter-label'));

                if (stripos($itemLabel->getText(), $parameterLabel) !== false) {
                    return $item;
                }
            } catch (\Facebook\WebDriver\Exception\NoSuchElementException $ex) {
                continue;
            }
        }

        $message = 'Unable to find parameter with label "' . $parameterLabel . '" in product filter.';
        throw new \Facebook\WebDriver\Exception\NoSuchElementException($message);
    }

    /**
     * @param \Facebook\WebDriver\WebDriverElement $parameterElement
     * @param string $parameterValueText
     * @return \Facebook\WebDriver\WebDriverElement
     */
    private function getLabelElementByParameterValueText($parameterElement, $parameterValueText) {
        $labelElements = $parameterElement->findElements(WebDriverBy::cssSelector('.js-product-filter-parameter-value'));

        foreach ($labelElements as $labelElement) {
            try {
                if (stripos($labelElement->getText(), $parameterValueText) !== false) {
                    return $labelElement;
                }
            } catch (\Facebook\WebDriver\Exception\NoSuchElementException $ex) {
                continue;
            }
        }

        $message = 'Unable to find parameter value with label "' . $parameterValueText . '" in product filter.';
        throw new \Facebook\WebDriver\Exception\NoSuchElementException($message);
    }
}
