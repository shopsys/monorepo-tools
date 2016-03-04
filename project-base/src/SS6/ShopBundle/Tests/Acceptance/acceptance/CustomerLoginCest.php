<?php

namespace SS6\ShopBundle\Tests\Acceptance\acceptance;

use SS6\ShopBundle\Tests\Acceptance\acceptance\PageObject\Front\LoginPage;
use SS6\ShopBundle\Tests\Test\Codeception\AcceptanceTester;

class CustomerLoginCest {

	public function testLoginAsCustomerFromMainPage(LoginPage $loginPage, AcceptanceTester $me) {
		$me->wantTo('login as a customer from main page');
		$me->amOnPage('/');
		$me->clickByText('Přihlásit se');
		$loginPage->login('no-reply@netdevelo.cz', 'user123');
		$me->see('Jaromír Jágr');
		$me->clickByText('Odhlásit se');
		$me->see('Přihlásit se');
		$me->seeCurrentPageEquals('/');
	}

	public function testLoginAsCustomerFromCategoryPage(LoginPage $loginPage, AcceptanceTester $me) {
		$me->wantTo('login as a customer from category page');
		$me->amOnPage('/pocitace-prislusenstvi/');
		$me->clickByText('Přihlásit se');
		$loginPage->login('no-reply@netdevelo.cz', 'user123');
		$me->see('Jaromír Jágr');
		$me->clickByText('Odhlásit se');
		$me->see('Přihlásit se');
		$me->seeCurrentPageEquals('/');
	}

	public function testLoginAsCustomerFromLoginPage(LoginPage $loginPage, AcceptanceTester $me) {
		$me->wantTo('login as a customer from login page');
		$me->amOnPage('/prihlaseni/');
		$loginPage->login('no-reply@netdevelo.cz', 'user123');
		$me->see('Jaromír Jágr');
		$me->clickByText('Odhlásit se');
		$me->see('Přihlásit se');
		$me->seeCurrentPageEquals('/');
	}

}
