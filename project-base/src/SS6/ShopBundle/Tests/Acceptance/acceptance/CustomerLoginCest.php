<?php

namespace SS6\ShopBundle\Tests\Acceptance\acceptance;

use SS6\ShopBundle\Tests\Test\Codeception\AcceptanceTester;

class CustomerLoginCest {

	public function testLoginAsCustomerFromMainPage(AcceptanceTester $me) {
		$me->wantTo('login as a customer from main page');
		$me->amOnPage('/');
		$me->click('Přihlásit se');
		$me->fillField('input[name="front_login_form[email]"]', 'no-reply@netdevelo.cz');
		$me->fillField('input[name="front_login_form[password]"]', 'user123');
		$me->click('button[name="front_login_form[login]"]');
		$me->waitForAjax();
		$me->see('Jaromír Jágr');
		$me->click('Odhlásit se');
		$me->see('Přihlásit se');
	}

	public function testLoginAsCustomerFromCategoryPage(AcceptanceTester $me) {
		$me->wantTo('login as a customer from category page');
		$me->amOnPage('/pocitace-prislusenstvi/');
		$me->click('Přihlásit se');
		$me->fillField('input[name="front_login_form[email]"]', 'no-reply@netdevelo.cz');
		$me->fillField('input[name="front_login_form[password]"]', 'user123');
		$me->click('button[name="front_login_form[login]"]');
		$me->waitForAjax();
		$me->see('Jaromír Jágr');
		$me->click('Odhlásit se');
		$me->see('Přihlásit se');
		$me->seeCurrentPageEquals('/');
	}

}
