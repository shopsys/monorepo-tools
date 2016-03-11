<?php

namespace SS6\ShopBundle\Tests\Acceptance\acceptance\PageObject\Front;

use Facebook\WebDriver\WebDriverBy;
use SS6\ShopBundle\Tests\Acceptance\acceptance\PageObject\AbstractPage;
use SS6\ShopBundle\Tests\Acceptance\acceptance\PageObject\Front\ProductListComponent;
use SS6\ShopBundle\Tests\Test\Codeception\AcceptanceTester;
use SS6\ShopBundle\Tests\Test\Codeception\Module\StrictWebDriver;

class HomepagePage extends AbstractPage {

	/**
	 * @var \SS6\ShopBundle\Tests\Acceptance\acceptance\PageObject\Front\ProductListComponent
	 */
	private $productListComponent;

	public function __construct(
		StrictWebDriver $strictWebDriver,
		AcceptanceTester $tester,
		ProductListComponent $productListComponent
	) {
		$this->productListComponent = $productListComponent;
		parent::__construct($strictWebDriver, $tester);
	}

	/**
	 * @param string $productName
	 * @param int $quantity
	 */
	public function addTopProductToCartByName($productName, $quantity = 1) {
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
