<?php

namespace SS6\ShopBundle\Tests\Acceptance\acceptance;

use Facebook\WebDriver\WebDriverBy;
use SS6\ShopBundle\Tests\Acceptance\acceptance\PageObject\Front\CartPage;
use SS6\ShopBundle\Tests\Acceptance\acceptance\PageObject\Front\HomepagePage;
use SS6\ShopBundle\Tests\Acceptance\acceptance\PageObject\Front\ProductDetailPage;
use SS6\ShopBundle\Tests\Acceptance\acceptance\PageObject\Front\ProductListPage;
use SS6\ShopBundle\Tests\Test\Codeception\AcceptanceTester;

class CartCest {

	public function testAddingSameProductToCartMakesSum(
		CartPage $cartPage,
		ProductDetailPage $productDetailPage,
		AcceptanceTester $me
	) {
		$me->wantTo('have more pieces of the same product as one item in cart');
		$me->amOnPage('/22-sencor-sle-22f46dm4-hello-kitty/');

		$productDetailPage->addProductIntoCart(3);
		$me->see('1 položka za 10 497,00 Kč', WebDriverBy::cssSelector('#cart-box'));

		$productDetailPage->addProductIntoCart(3);
		$me->see('1 položka za 20 994,00 Kč', WebDriverBy::cssSelector('#cart-box'));

		$me->amOnPage('/kosik/');

		$quantityField = $cartPage->getQuantityFieldByProductName('22" Sencor SLE 22F46DM4 HELLO KITTY');
		$me->seeInFieldByElement(6, $quantityField);
	}

	public function testAddToCartFromProductListPage(CartPage $cartPage, ProductListPage $productListPage, AcceptanceTester $me) {
		$me->wantTo('add product to cart from product list');
		$me->amOnPage('/televize-audio/');
		$productListPage->addProductToCartByName('Defender 2.0 SPK-480', 1);
		$me->see('Do košíku bylo vloženo zboží');
		$me->seeInCss('1 položka', '.cart-box__info');
		$me->amOnPage('/kosik/');
		$productPriceColumn = $cartPage->getProductPriceColumnByName('Defender 2.0 SPK-480');
		$me->seeInElement('119,00 Kč', $productPriceColumn);
	}

	public function testAddToCartFromHomepage(CartPage $cartPage, HomepagePage $homepagePage, AcceptanceTester $me) {
		$me->wantTo('add product to cart from homepage');
		$me->amOnPage('/');
		$homepagePage->addProductToCartByName('22" Sencor SLE 22F46DM4 HELLO KITTY', 1);
		$me->see('Do košíku bylo vloženo zboží');
		$me->seeInCss('1 položka', '.cart-box__info');
		$me->amOnPage('/kosik/');
		$productPrice = $cartPage->getProductPriceColumnByName('22" Sencor SLE 22F46DM4 HELLO KITTY');
		$me->seeInElement('3 499,00 Kč', $productPrice);
	}

	public function testAddToCartFromProductDetail(ProductDetailPage $productDetailPage, AcceptanceTester $me) {
		$me->wantTo('add product to cart from product detail');
		$me->amOnPage('/22-sencor-sle-22f46dm4-hello-kitty/');
		$me->see('Vložit do košíku');
		$productDetailPage->addProductIntoCart(3);
		$me->see('Do košíku bylo vloženo zboží');
		$me->clickByCss('.window-button-close');
		$me->seeInCss('1 položka za 10 497,00 Kč', '.cart-box__info');
		$me->amOnPage('/kosik/');
		$me->see('22" Sencor SLE 22F46DM4 HELLO KITTY');
	}

	public function testChangeCartItemAndRecalculatePrice(
		CartPage $cartPage,
		ProductDetailPage $productDetailPage,
		AcceptanceTester $me
	) {
		$me->wantTo('change items in cart and recalculate price');
		$me->amOnPage('/22-sencor-sle-22f46dm4-hello-kitty/');
		$me->see('Vložit do košíku');
		$productDetailPage->addProductIntoCart(3);
		$me->clickByText('Přejít do košíku');

		$quantityField = $cartPage->getQuantityFieldByProductName('22" Sencor SLE 22F46DM4 HELLO KITTY');
		$me->fillFieldByElement($quantityField, 10);
		$me->waitForAjax();
		$me->clickByText('Přepočítat');
		$me->waitForAjax();
		$orderPriceColumn = $cartPage->getTotalProductsPriceColumn();
		$me->seeInElement('Celková cena s DPH: 34 990,00 Kč', $orderPriceColumn);
	}

}
