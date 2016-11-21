<?php

namespace SS6\ShopBundle\Tests\Acceptance\acceptance\PageObject\Front;

use SS6\ShopBundle\Tests\Acceptance\acceptance\PageObject\AbstractPage;

class LayoutPage extends AbstractPage {

	/**
	 * @param string $email
	 * @param string $password
	 */
	public function openLoginPopup() {
		$this->tester->clickByText('Přihlásit se');
		$this->tester->wait(1); // wait for SS6.window to show
	}

}
