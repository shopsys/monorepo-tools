<?php

namespace SS6\ShopBundle\Tests\Acceptance\acceptance;

use SS6\ShopBundle\Tests\Test\Codeception\AcceptanceTester;

class LoginCest {

	public function testLoginAdmin(AcceptanceTester $me) {
		$me->wantTo('login on admin');
		$me->amOnPage('/admin/');
		$me->see('Administrace');
		$me->fillFieldByName('admin_login_form[username]', 'admin');
		$me->fillFieldByName('admin_login_form[password]', 'admin123');
		$me->clickByText('Přihlásit');
		$me->see('Nástěnka');
	}

	public function testLoginFront(AcceptanceTester $me) {
		$me->wantTo('login on FE');
		$me->amOnPage('/prihlaseni/');
		$me->see('Přihlášení');
		$me->fillFieldByName('front_login_form[email]', 'no-reply@netdevelo.cz');
		$me->fillFieldByName('front_login_form[password]', 'user123');
		$me->clickByText('Přihlásit');
		$me->see('Jaromír');
	}

}
