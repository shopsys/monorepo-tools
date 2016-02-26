<?php

namespace SS6\ShopBundle\Tests\Acceptance\acceptance;

use Facebook\WebDriver\WebDriverBy;
use SS6\ShopBundle\Tests\Acceptance\acceptance\PageObject\CartPage;
use SS6\ShopBundle\Tests\Test\Codeception\AcceptanceTester;

class CartCest {

	public function testAddingSameProductToCartMakesSum(CartPage $cartPage, AcceptanceTester $me) {
		$me->wantTo('have more pieces of the same product as one item in cart');
		$me->amOnPage('/22-sencor-sle-22f46dm4-hello-kitty/');
		$me->fillFieldByCss('.js-product-detail-main-add-to-cart-wrapper input[name="add_product_form[quantity]"]', '3');
		$me->clickByText('Vložit do košíku', WebDriverBy::cssSelector('.js-product-detail-main-add-to-cart-wrapper'));
		$me->waitForAjax();
		$me->see('1 položka za 10 497,00 Kč', WebDriverBy::cssSelector('#cart-box'));

		$me->fillFieldByCss('.js-product-detail-main-add-to-cart-wrapper input[name="add_product_form[quantity]"]', '3');
		$me->clickByText('Vložit do košíku', WebDriverBy::cssSelector('.js-product-detail-main-add-to-cart-wrapper'));
		$me->waitForAjax();
		$me->see('1 položka za 20 994,00 Kč', WebDriverBy::cssSelector('#cart-box'));

		$me->amOnPage('/kosik/');

		$quantityField = $cartPage->getQuantityFieldByProductName('22" Sencor SLE 22F46DM4 HELLO KITTY');
		$me->seeInFieldByElement($quantityField, 6);
	}

}
