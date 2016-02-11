<?php

namespace SS6\ShopBundle\Tests\Acceptance\acceptance;

use SS6\ShopBundle\Tests\Test\Codeception\AcceptanceTester;

class CustomerRegistrationCest {

	public function testSuccessfulRegistration(AcceptanceTester $me) {
		$me->wantTo('successfully register new customer');
		$me->amOnPage('/');
		$me->clickByText('Registrace');
		$me->fillField('input[name="registration_form[firstName]"]', 'Roman');
		$me->fillField('input[name="registration_form[lastName]"]', 'Štěpánek');
		$me->fillField('input[name="registration_form[email]"]', 'no-reply.16@netdevelo.cz');
		$me->fillField('input[name="registration_form[password][first]"]', 'user123');
		$me->fillField('input[name="registration_form[password][second]"]', 'user123');
		$me->wait(5);
		$me->clickByName('registration_form[save]');
		$me->see('Byli jste úspěšně zaregistrováni');
		$me->see('Roman Štěpánek');
		$me->see('Odhlásit se');
	}

	public function testAlreadyUsedEmail(AcceptanceTester $me) {
		$me->wantTo('use already used email while registration');
		$me->amOnPage('/registrace/');
		$me->fillField('input[name="registration_form[firstName]"]', 'Roman');
		$me->fillField('input[name="registration_form[lastName]"]', 'Štěpánek');
		$me->fillField('input[name="registration_form[email]"]', 'no-reply@netdevelo.cz');
		$me->fillField('input[name="registration_form[password][first]"]', 'user123');
		$me->fillField('input[name="registration_form[password][second]"]', 'user123');
		$me->wait(5);
		$me->clickByName('registration_form[save]');
		$me->waitForAjax();
		$me->see('V databázi se již nachází zákazník s tímto e-mailem');
	}

	public function testPasswordMismatch(AcceptanceTester $me) {
		$me->wantTo('use mismatching passwords while registration');
		$me->amOnPage('/registrace/');
		$me->fillField('input[name="registration_form[firstName]"]', 'Roman');
		$me->fillField('input[name="registration_form[lastName]"]', 'Štěpánek');
		$me->fillField('input[name="registration_form[email]"]', 'no-reply.16@netdevelo.cz');
		$me->fillField('input[name="registration_form[password][first]"]', 'user123');
		$me->fillField('input[name="registration_form[password][second]"]', 'wrongPassword');
		$me->wait(5);
		$me->clickByName('registration_form[save]');
		$me->waitForAjax();
		$me->see('Hesla se neshodují');
	}

}
