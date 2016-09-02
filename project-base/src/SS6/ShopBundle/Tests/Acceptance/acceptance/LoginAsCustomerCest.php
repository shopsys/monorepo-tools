<?php

namespace SS6\ShopBundle\Tests\Acceptance\acceptance;

use SS6\ShopBundle\Tests\Test\Codeception\AcceptanceTester;

class LoginAsCustomerCest {

	public function testLoginAsCustomer(AcceptanceTester $me) {
		$me->wantTo('login as a customer from admin');
		$me->loginAsAdmin('admin', 'admin123');
		$me->amOnPage('/admin/customer/edit/2');
		$me->clickByText('Přihlásit za uživatele');
		$me->switchToLastOpenedWindow();
		$me->seeCurrentPageEquals('/');
		$me->see('Pozor! Jste jako administrátor přihlášen za zákazníka.');
		$me->see('Igor Anpilogov');
	}

}
