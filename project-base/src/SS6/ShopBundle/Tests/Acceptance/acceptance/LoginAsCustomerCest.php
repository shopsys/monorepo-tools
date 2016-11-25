<?php

namespace SS6\ShopBundle\Tests\Acceptance\acceptance;

use SS6\ShopBundle\Tests\Acceptance\acceptance\PageObject\Admin\LoginPage;
use SS6\ShopBundle\Tests\Test\Codeception\AcceptanceTester;

class LoginAsCustomerCest {

	public function testLoginAsCustomer(AcceptanceTester $me, LoginPage $loginPage) {
		$me->wantTo('login as a customer from admin');
		$loginPage->login(LoginPage::ADMIN_USERNAME, LoginPage::ADMIN_PASSWORD);
		$me->amOnPage('/admin/customer/edit/2');
		$me->clickByText('Přihlásit za uživatele');
		$me->switchToLastOpenedWindow();
		$me->seeCurrentPageEquals('/');
		$me->see('Pozor! Jste jako administrátor přihlášen za zákazníka.');
		$me->see('Igor Anpilogov');
	}

}
