<?php

namespace SS6\ShopBundle\Tests\Acceptance\acceptance;

use SS6\ShopBundle\Tests\Test\Codeception\AcceptanceTester;

class LoginAsCustomerCest {

	public function testLoginAsCustomer(AcceptanceTester $me) {
		$me->wantTo('login on admin');
		$me->amOnPage('/admin/');
		$me->see('Administrace');
		$me->fillField('admin_login_form[username]', 'admin');
		$me->fillField('admin_login_form[password]', 'admin123');
		$me->click('Přihlásit');
		$me->click('Zákazníci');
		$me->click('Anpilogov Igor');
		$me->click('Přihlásit za uživatele');
		$me->switchToLastOpenedWindow();
		$me->see('Internetový obchod');
		$me->see('Igor Anpilogov');
	}

}
