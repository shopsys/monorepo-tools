<?php

namespace SS6\ShopBundle\Tests\Acceptance\acceptance\PageObject\Front;

use Facebook\WebDriver\WebDriverBy;
use SS6\ShopBundle\Tests\Acceptance\acceptance\PageObject\Front\ProductListComponent;
use SS6\ShopBundle\Tests\Test\Codeception\Module\StrictWebDriver;

class ProductListPage {

	/**
	 * @var \Facebook\WebDriver\WebDriver
	 */
	private $webDriver;

	/**
	 * @var \SS6\ShopBundle\Tests\Acceptance\acceptance\PageObject\Front\ProductListComponent
	 */
	private $productListComponent;

	public function __construct(
		StrictWebDriver $webDriver,
		ProductListComponent $productListComponent
	) {
		$this->webDriver = $webDriver->webDriver;
		$this->productListComponent = $productListComponent;
	}

	/**
	 * @param string $productName
	 * @param int $quantity
	 */
	public function addProductToCartByName($productName, $quantity = 1) {
		$context = $this->getProductListCompomentContext();

		$this->productListComponent->addProductToCartByName($productName, $quantity, $context);
	}

	/**
	 * @return \Facebook\WebDriver\WebDriverElement
	 */
	private function getProductListCompomentContext() {
		return $this->webDriver->findElement(WebDriverBy::cssSelector('.main-content'));
	}

}
