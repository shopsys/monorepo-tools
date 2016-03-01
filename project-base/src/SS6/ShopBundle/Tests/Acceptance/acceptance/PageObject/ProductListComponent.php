<?php

namespace SS6\ShopBundle\Tests\Acceptance\acceptance\PageObject;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverElement;
use SS6\ShopBundle\Tests\Test\Codeception\AcceptanceTester;
use SS6\ShopBundle\Tests\Test\Codeception\Module\StrictWebDriver;

class ProductListComponent {

	/**
	 * @var \Facebook\WebDriver\WebDriver
	 */
	private $webDriver;

	/**
	 * @var \SS6\ShopBundle\Tests\Test\Codeception\AcceptanceTester
	 */
	private $tester;

	public function __construct(
		StrictWebDriver $webDriver,
		AcceptanceTester $tester
	) {
		$this->webDriver = $webDriver->webDriver;
		$this->tester = $tester;
	}

	/**
	 * @param string $productName
	 * @param int $quantity
	 * @param \Facebook\WebDriver\WebDriverElement $context
	 */
	public function addProductToCartByName($productName, $quantity, WebDriverElement $context) {
		$productItemElement = $this->findProductListItemByName($productName, $context);

		$quantityElement = $productItemElement->findElement(WebDriverBy::name('add_product_form[quantity]'));
		$addButtonElement = $productItemElement->findElement(WebDriverBy::name('add_product_form[add]'));

		$this->tester->fillFieldByElement($quantityElement, $quantity);
		$this->tester->clickByElement($addButtonElement);
		$this->tester->waitForAjax();
	}

	/**
	 * @param string $productName
	 * @param \Facebook\WebDriver\WebDriverElement $context
	 * @return \Facebook\WebDriver\WebDriverElement
	 */
	private function findProductListItemByName($productName, WebDriverElement $context) {
		$productItems = $context->findElements(WebDriverBy::cssSelector('.js-product-list li[class^="list-items__item"]'));

		foreach ($productItems as $item) {
			try {
				$nameElement = $item->findElement(WebDriverBy::cssSelector('h2[class="list-items__title"]'));

				if ($nameElement->getText() === $productName) {
					return $item;
				}
			} catch (\Facebook\WebDriver\Exception\NoSuchElementException $ex) {
				continue;
			}
		}

		$message = 'Unable to find product "' . $productName . '" in product list component.';
		throw new \Facebook\WebDriver\Exception\NoSuchElementException($message);
	}

}
