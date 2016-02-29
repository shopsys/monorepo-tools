<?php

namespace SS6\ShopBundle\Tests\Acceptance\acceptance\PageObject;

use Facebook\WebDriver\WebDriverBy;
use SS6\ShopBundle\Tests\Test\Codeception\Module\StrictWebDriver;

class CartPage {

	/**
	 * @var \Facebook\WebDriver\WebDriver
	 */
	private $webDriver;

	public function __construct(StrictWebDriver $webDriver) {
		$this->webDriver = $webDriver->webDriver;
	}

	/**
	 * @param string $productName
	 * @return \Facebook\WebDriver\WebDriverElement
	 */
	public function getQuantityFieldByProductName($productName) {
		$row = $this->findProductRowInCartByName($productName);

		return $row->findElement(WebDriverBy::cssSelector('input[name^="cart_form[quantities]"]'));
	}

	/**
	 * @param string $productName
	 * @return \Facebook\WebDriver\WebDriverElement
	 */
	private function findProductRowInCartByName($productName) {
		$rows = $this->webDriver->findElements(WebDriverBy::cssSelector('.table-cart tr'));

		foreach ($rows as $row) {
			try {
				$nameCell = $row->findElement(WebDriverBy::cssSelector('.table-cart__title'));

				if ($nameCell->getText() === $productName) {
					return $row;
				}
			} catch (\Facebook\WebDriver\Exception\NoSuchElementException $ex) {
				continue;
			}
		}

		$message = 'Unable to find row containing product "' . $productName . '" in cart.';
		throw new \Facebook\WebDriver\Exception\NoSuchElementException($message);
	}

}
