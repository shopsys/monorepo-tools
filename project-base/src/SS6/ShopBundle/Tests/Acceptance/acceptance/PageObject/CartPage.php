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
		$table = $this->webDriver->findElement(WebDriverBy::cssSelector('.table-cart'));

		$rows = $table->findElements(WebDriverBy::cssSelector('tr'));

		foreach ($rows as $row) {
			try {
				$nameCell = $row->findElement(WebDriverBy::cssSelector('.table-cart__title'));

				if ($nameCell->getText() === $productName) {
					$quantityInput = $row->findElement(WebDriverBy::cssSelector('input[name^="cart_form[quantities]"]'));
					return $quantityInput;
				}
			} catch (\Facebook\WebDriver\Exception\NoSuchElementException $ex) {
				continue;
			}
		}

		$message = 'Unable to find quantity input in cart for product "' . $productName . '"';
		throw new \Facebook\WebDriver\Exception\NoSuchElementException($message);
	}

}
