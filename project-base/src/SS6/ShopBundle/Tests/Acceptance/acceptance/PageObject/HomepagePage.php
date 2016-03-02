<?php

namespace SS6\ShopBundle\Tests\Acceptance\acceptance\PageObject;

use Facebook\WebDriver\WebDriverBy;
use SS6\ShopBundle\Tests\Acceptance\acceptance\PageObject\ProductListComponent;
use SS6\ShopBundle\Tests\Test\Codeception\Module\StrictWebDriver;

class HomepagePage {

	/**
	 * @var \Facebook\WebDriver\WebDriver
	 */
	private $webDriver;

	/**
	 * @var \SS6\ShopBundle\Tests\Acceptance\acceptance\PageObject\ProductListComponent
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
		$topProductsContext = $this->getTopProductsContext();

		$this->productListComponent->addProductToCartByName($productName, $quantity, $topProductsContext);
	}

	/**
	 * @return \Facebook\WebDriver\WebDriverElement
	 */
	private function getTopProductsContext() {
		return $this->webDriver->findElement(WebDriverBy::cssSelector('#top-products'));
	}

}
