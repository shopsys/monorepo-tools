<?php

namespace SS6\ShopBundle\Tests\Acceptance\acceptance;

use SS6\ShopBundle\Tests\Test\Codeception\AcceptanceTester;

class LoginAsCustomerCest {

	public function testLoginAsCustomer(AcceptanceTester $me) {
		$me->wantTo('login as a customer from admin');
		$me->amOnPage('/admin/');
		$me->see('Administrace');
		$me->fillField('admin_login_form[username]', 'admin');
		$me->fillField('admin_login_form[password]', 'admin123');
		$me->clickByText('Přihlásit');
		$me->amOnPage('/admin/customer/edit/3');
		$me->clickByText('Přihlásit za uživatele');
		$me->switchToLastOpenedWindow();
		$me->see('Internetový obchod');
		$me->see('Igor Anpilogov');
	}

}
