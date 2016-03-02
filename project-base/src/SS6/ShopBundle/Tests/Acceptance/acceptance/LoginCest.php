<?php

namespace SS6\ShopBundle\Tests\Acceptance\acceptance;

use SS6\ShopBundle\Tests\Test\Codeception\AcceptanceTester;

class LoginCest {

	public function testSuccessfulLoginAdmin(AcceptanceTester $me) {
		$me->wantTo('login on admin with valid data');
		$me->amOnPage('/admin/');
		$me->see('Administrace');
		$me->fillFieldByName('admin_login_form[username]', 'admin');
		$me->fillFieldByName('admin_login_form[password]', 'admin123');
		$me->clickByText('Přihlásit');
		$me->see('Nástěnka');
	}

	public function testLoginWithInvalidUsernameAdmin(AcceptanceTester $me) {
		$me->wantTo('login on admin with nonexistent username');
		$me->amOnPage('/admin/');
		$me->see('Administrace');
		$me->fillFieldByName('admin_login_form[username]', 'nonexistent username');
		$me->fillFieldByName('admin_login_form[password]', 'password');
		$me->clickByText('Přihlásit');
		$me->see('Přihlášení se nepodařilo.');
		$me->seeCurrentPageEquals('/admin/');
	}

	public function testLoginWithInvalidPasswordAdmin(AcceptanceTester $me) {
		$me->wantTo('login on admin with invalid password');
		$me->amOnPage('/admin/');
		$me->see('Administrace');
		$me->fillFieldByName('admin_login_form[username]', 'admin');
		$me->fillFieldByName('admin_login_form[password]', 'invalid password');
		$me->clickByText('Přihlásit');
		$me->see('Přihlášení se nepodařilo.');
		$me->seeCurrentPageEquals('/admin/');
	}

}
