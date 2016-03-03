<?php

namespace SS6\ShopBundle\Tests\Acceptance\acceptance;

use SS6\ShopBundle\Tests\Acceptance\acceptance\PageObject\Admin\LoginPage;
use SS6\ShopBundle\Tests\Test\Codeception\AcceptanceTester;

class LoginCest {

	public function testSuccessfulLoginAdmin(LoginPage $loginPage, AcceptanceTester $me) {
		$me->wantTo('login on admin with valid data');
		$me->amOnPage('/admin/');
		$me->see('Administrace');
		$loginPage->login('admin', 'admin123');
		$me->see('Nástěnka');
	}

	public function testLoginWithInvalidUsernameAdmin(LoginPage $loginPage, AcceptanceTester $me) {
		$me->wantTo('login on admin with nonexistent username');
		$me->amOnPage('/admin/');
		$me->see('Administrace');
		$loginPage->login('nonexistent username', 'admin123');
		$me->see('Přihlášení se nepodařilo.');
		$me->seeCurrentPageEquals('/admin/');
	}

	public function testLoginWithInvalidPasswordAdmin(LoginPage $loginPage, AcceptanceTester $me) {
		$me->wantTo('login on admin with invalid password');
		$me->amOnPage('/admin/');
		$me->see('Administrace');
		$loginPage->login('admin', 'invalid password');
		$me->see('Přihlášení se nepodařilo.');
		$me->seeCurrentPageEquals('/admin/');
	}

}
