<?php

namespace SS6\ShopBundle\Tests\Acceptance\acceptance;

use SS6\ShopBundle\Tests\Acceptance\acceptance\PageObject\Admin\LoginPage;
use SS6\ShopBundle\Tests\Test\Codeception\AcceptanceTester;

class LoginAsCustomerCest {

	public function testLoginAsCustomer(LoginPage $loginPage, AcceptanceTester $me) {
		$me->wantTo('login as a customer from admin');
		$me->amOnPage('/admin/');
		$me->see('Administrace');
		$loginPage->login('admin', 'admin123');
		$me->amOnPage('/admin/customer/edit/3');
		$me->clickByText('Přihlásit za uživatele');
		$me->switchToLastOpenedWindow();
		$me->see('Internetový obchod');
		$me->see('Igor Anpilogov');
	}

}
