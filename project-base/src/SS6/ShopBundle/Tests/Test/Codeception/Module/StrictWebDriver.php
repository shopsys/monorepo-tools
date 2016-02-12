<?php

namespace SS6\ShopBundle\Tests\Test\Codeception\Module;

use Codeception\Module\WebDriver;
use Codeception\Util\Locator;
use Symfony\Component\DomCrawler\Crawler;

class StrictWebDriver extends WebDriver {

	/**
	 * @param string[] $alternatives
	 * @return string
	 */
	private function getDeprecatedMethodExceptionMessage(array $alternatives) {
		$messageWithAlternativesPlaceholder = 'This method is deprecated because it uses fuzzy locators. '
			. 'Use one of strict alternatives instead: %s. Or implement new method with strict locator.';

		return sprintf(
			$messageWithAlternativesPlaceholder,
			implode(', ', $alternatives)
		);
	}

	/**
	 * @deprecated
	 */
	public function click($link, $context = null) {
		$strictAlternatives = [
			'clickByText',
			'clickByName',
		];
		$message = $this->getDeprecatedMethodExceptionMessage($strictAlternatives);
		throw new \SS6\ShopBundle\Tests\Test\Codeception\Exception\DeprecatedMethodException($message);
	}

	/**
	 * @param string $text
	 */
	public function clickByText($text) {
		$locator = Crawler::xpathLiteral(trim($text));

		$xpath = Locator::combine(
			".//a[normalize-space(.)=$locator]",
			".//button[normalize-space(.)=$locator]",
			".//a/img[normalize-space(@alt)=$locator]/ancestor::a",
			".//input[./@type = 'submit' or ./@type = 'image' or ./@type = 'button'][normalize-space(@value)=$locator]"
		);

		parent::click(['xpath' => $xpath]);
	}

	/**
	 * @param string $name
	 */
	public function clickByName($name) {
		$locator = Crawler::xpathLiteral(trim($name));

		$xpath = Locator::combine(
			".//input[./@type = 'submit' or ./@type = 'image' or ./@type = 'button'][./@name = $locator]",
			".//button[./@name = $locator]"
		);

		parent::click(['xpath' => $xpath]);
	}

	/**
	 * @deprecated
	 */
	public function fillField($field, $value) {
		$strictAlternatives = [
			'fillFieldByName',
		];
		$message = $this->getDeprecatedMethodExceptionMessage($strictAlternatives);
		throw new \SS6\ShopBundle\Tests\Test\Codeception\Exception\DeprecatedMethodException($message);
	}

	/**
	 * @param string $fieldName
	 * @param string $value
	 */
	public function fillFieldByName($fieldName, $value) {
		$locator = Crawler::xpathLiteral(trim($fieldName));
		$xpath = ".//*[self::input | self::textarea | self::select][@name = $locator]";

		parent::fillField(['xpath' => $xpath], $value);
	}

	/**
	 * @deprecated
	 */
	public function seeCheckboxIsChecked($checkbox) {
		$strictAlternatives = [
			'seeCheckboxIsCheckedById',
		];
		$message = $this->getDeprecatedMethodExceptionMessage($strictAlternatives);
		throw new \SS6\ShopBundle\Tests\Test\Codeception\Exception\DeprecatedMethodException($message);
	}

	/**
	 * @param string $checkboxId
	 */
	public function seeCheckboxIsCheckedById($checkboxId) {
		$locator = Crawler::xpathLiteral(trim($checkboxId));
		$xpath = ".//input[@type = 'checkbox'][./@id = $locator]";

		parent::seeCheckboxIsChecked(['xpath' => $xpath]);
	}

	/**
	 * @deprecated
	 */
	public function dontSeeCheckboxIsChecked($checkbox) {
		$strictAlternatives = [
			'dontSeeCheckboxIsCheckedById',
		];
		$message = $this->getDeprecatedMethodExceptionMessage($strictAlternatives);
		throw new \SS6\ShopBundle\Tests\Test\Codeception\Exception\DeprecatedMethodException($message);
	}

	/**
	 * @param string $checkboxId
	 */
	public function dontSeeCheckboxIsCheckedById($checkboxId) {
		$locator = Crawler::xpathLiteral(trim($checkboxId));
		$xpath = ".//input[@type = 'checkbox'][./@id = $locator]";

		parent::dontSeeCheckboxIsChecked(['xpath' => $xpath]);
	}

	/**
	 * @deprecated
	 */
	public function checkOption($option) {
		$strictAlternatives = [
			'checkOptionById',
		];
		$message = $this->getDeprecatedMethodExceptionMessage($strictAlternatives);
		throw new \SS6\ShopBundle\Tests\Test\Codeception\Exception\DeprecatedMethodException($message);
	}

	/**
	 * @param string $optionId
	 */
	public function checkOptionById($optionId) {
		$locator = Crawler::xpathLiteral(trim($optionId));
		$xpath = ".//input[@type = 'checkbox' or @type = 'radio'][./@id = $locator]";

		parent::checkOption(['xpath' => $xpath]);
	}

	/**
	 * @deprecated
	 */
	public function seeInField($field, $value) {
		$strictAlternatives = [
			'seeInFieldByName',
		];
		$message = $this->getDeprecatedMethodExceptionMessage($strictAlternatives);
		throw new \SS6\ShopBundle\Tests\Test\Codeception\Exception\DeprecatedMethodException($message);
	}

	/**
	 * @param string $fieldName
	 * @param string $value
	 */
	public function seeInFieldByName($fieldName, $value) {
		$locator = Crawler::xpathLiteral(trim($fieldName));
		$xpath = ".//*[self::input | self::textarea | self::select][@name = $locator]";

		parent::seeInField(['xpath' => $xpath], $value);
	}

}
