<?php

namespace SS6\ShopBundle\Tests\Acceptance\acceptance\PageObject\Admin;

use SS6\ShopBundle\Tests\Acceptance\acceptance\PageObject\AbstractPage;

class LoginPage extends AbstractPage {

	public function login($username, $password) {
		$this->tester->fillFieldByName('admin_login_form[username]', $username);
		$this->tester->fillFieldByName('admin_login_form[password]', $password);
		$this->tester->clickByText('Přihlásit');
	}

}
