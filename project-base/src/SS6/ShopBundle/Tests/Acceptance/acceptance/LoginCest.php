<?php

namespace SS6\ShopBundle\Tests\Acceptance\acceptance;

use SS6\ShopBundle\Tests\Test\Codeception\AcceptanceTester;

class LoginCest {

	public function testSuccessfulLoginAdmin(AcceptanceTester $me) {
		$me->wantTo('login on admin with valid data');
		$me->loginAsAdmin('admin', 'admin123');
		$me->see('Nástěnka');
	}

	public function testLoginWithInvalidUsernameAdmin(AcceptanceTester $me) {
		$me->wantTo('login on admin with nonexistent username');
		$me->loginAsAdmin('nonexistent username', 'admin123');
		$me->see('Přihlášení se nepodařilo.');
		$me->seeCurrentPageEquals('/admin/');
	}

	public function testLoginWithInvalidPasswordAdmin(AcceptanceTester $me) {
		$me->wantTo('login on admin with invalid password');
		$me->loginAsAdmin('nonexistent username', 'invalid password');
		$me->see('Přihlášení se nepodařilo.');
		$me->seeCurrentPageEquals('/admin/');
	}

}
