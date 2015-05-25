<?php

namespace SS6\ShopBundle\Tests\Acceptance\acceptance;

use SS6\ShopBundle\Tests\Test\Codeception\AcceptanceTester;

class ShoppingCest {

	public function testAddToCart(AcceptanceTester $me) {
		$me->wantTo('add product to cart');
		$me->amOnPage('/televize-audio/');
		$me->see('Vložit do košíku');
		$me->click('Vložit do košíku');
		$me->waitForJS('return $.active == 0;', 15);
		$me->see('Do košíku bylo vloženo zboží');
		$me->click('Přejít do košíku');
		$me->amOnPage('/kosik/');
		$me->see('Objednat');
	}

}
