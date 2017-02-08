<?php

namespace SS6\ShopBundle\Tests\Acceptance\acceptance\PageObject\Front;

use SS6\ShopBundle\Tests\Acceptance\acceptance\PageObject\AbstractPage;

class CartBoxPage extends AbstractPage {

	/**
	 * @param string $text
	 */
	public function seeInCartBox($text) {
		$this->tester->seeInCss($text, '.js-cart-info');
	}

}
