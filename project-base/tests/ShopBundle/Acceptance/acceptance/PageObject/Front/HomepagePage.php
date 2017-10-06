<?php

namespace Tests\ShopBundle\Acceptance\acceptance\PageObject\Front;

use Facebook\WebDriver\WebDriverBy;
use Tests\ShopBundle\Acceptance\acceptance\PageObject\AbstractPage;
use Tests\ShopBundle\Test\Codeception\AcceptanceTester;
use Tests\ShopBundle\Test\Codeception\Module\StrictWebDriver;

class HomepagePage extends AbstractPage
{
    /**
     * @var \Tests\ShopBundle\Acceptance\acceptance\PageObject\Front\ProductListComponent
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
    public function addTopProductToCartByName($productName, $quantity = 1)
    {
        $topProductsContext = $this->getTopProductsContext();

        $this->productListComponent->addProductToCartByName($productName, $quantity, $topProductsContext);
    }

    /**
     * @return \Facebook\WebDriver\WebDriverElement
     */
    private function getTopProductsContext()
    {
        return $this->webDriver->findElement(WebDriverBy::cssSelector('#top-products'));
    }
}
