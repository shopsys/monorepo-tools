<?php

namespace Shopsys\ShopBundle\Tests\Acceptance\acceptance\PageObject\Front;

use Facebook\WebDriver\WebDriverBy;
use Shopsys\ShopBundle\Tests\Acceptance\acceptance\PageObject\AbstractPage;
use Shopsys\ShopBundle\Tests\Acceptance\acceptance\PageObject\Front\ProductListComponent;
use Shopsys\ShopBundle\Tests\Test\Codeception\AcceptanceTester;
use Shopsys\ShopBundle\Tests\Test\Codeception\Module\StrictWebDriver;

class HomepagePage extends AbstractPage
{

    /**
     * @var \Shopsys\ShopBundle\Tests\Acceptance\acceptance\PageObject\Front\ProductListComponent
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
