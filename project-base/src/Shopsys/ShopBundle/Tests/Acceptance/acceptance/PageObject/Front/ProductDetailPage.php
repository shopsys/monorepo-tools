<?php

namespace Shopsys\ShopBundle\Tests\Acceptance\acceptance\PageObject\Front;

use Facebook\WebDriver\WebDriverBy;
use Shopsys\ShopBundle\Tests\Acceptance\acceptance\PageObject\AbstractPage;

class ProductDetailPage extends AbstractPage
{
    const PRODUCT_DETAIL_QUANTITY_INPUT = '.js-product-detail-main-add-to-cart-wrapper input[name="add_product_form[quantity]"]';
    const PRODUCT_DETAIL_MAIN_WRAPPER = '.js-product-detail-main-add-to-cart-wrapper';

    /**
     * @param int $quantity
     */
    public function addProductIntoCart($quantity = 1)
    {
        $this->tester->fillFieldByCss(
            self::PRODUCT_DETAIL_QUANTITY_INPUT,
            $quantity
        );
        $this->tester->clickByText('Vložit do košíku', WebDriverBy::cssSelector(self::PRODUCT_DETAIL_MAIN_WRAPPER));
        $this->tester->waitForAjax();
        $this->tester->wait(1); // animation of popup window
    }
}
