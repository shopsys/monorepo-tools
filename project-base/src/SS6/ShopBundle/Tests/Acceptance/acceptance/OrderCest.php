<?php

namespace SS6\ShopBundle\Tests\Acceptance\acceptance;

use SS6\ShopBundle\Tests\Test\Codeception\AcceptanceTester;

class OrderCest {

	public function testFormRemembersPaymentAndTransportWhenClickingBack(AcceptanceTester $me) {
		$me->wantTo('have my payment and transport remebered by order');

		$me->amOnPage('/televize-audio/');
		$me->clickByText('Vložit do košíku');
		$me->waitForAjax();
		$me->clickByText('Přejít do košíku');
		$me->clickByText('Objednat');

		$me->dontSeeCheckboxIsChecked('input[name="transport_and_payment_form[transport]"][value="1"]');
		$me->checkOption('input[name="transport_and_payment_form[transport]"][value="1"]');
		$me->dontSeeCheckboxIsChecked('input[name="transport_and_payment_form[payment]"][value="2"]');
		$me->checkOption('input[name="transport_and_payment_form[payment]"][value="2"]');
		$me->clickByText('Pokračovat v objednávce');
		$me->clickByText('Zpět na výběr dopravy a platby');

		$me->seeCheckboxIsChecked('input[name="transport_and_payment_form[transport]"][value="1"]');
		$me->seeCheckboxIsChecked('input[name="transport_and_payment_form[payment]"][value="2"]');
	}

	public function testFormRemembersPaymentAndTransportWhenGoingDirectlyToUrl(AcceptanceTester $me) {
		$me->wantTo('have my payment and transport remebered by order');

		$me->amOnPage('/televize-audio/');
		$me->clickByText('Vložit do košíku');
		$me->waitForAjax();
		$me->clickByText('Přejít do košíku');
		$me->clickByText('Objednat');

		$me->dontSeeCheckboxIsChecked('input[name="transport_and_payment_form[transport]"][value="1"]');
		$me->checkOption('input[name="transport_and_payment_form[transport]"][value="1"]');
		$me->dontSeeCheckboxIsChecked('input[name="transport_and_payment_form[payment]"][value="2"]');
		$me->checkOption('input[name="transport_and_payment_form[payment]"][value="2"]');
		$me->clickByText('Pokračovat v objednávce');
		$me->amOnPage('/objednavka/');

		$me->seeCheckboxIsChecked('input[name="transport_and_payment_form[transport]"][value="1"]');
		$me->seeCheckboxIsChecked('input[name="transport_and_payment_form[payment]"][value="2"]');
	}

	public function testFormRemembersFirstName(AcceptanceTester $me) {
		$me->wantTo('have my first name remebered by order');

		$me->amOnPage('/televize-audio/');
		$me->clickByText('Vložit do košíku');
		$me->waitForAjax();
		$me->clickByText('Přejít do košíku');
		$me->clickByText('Objednat');
		$me->checkOption('input[name="transport_and_payment_form[transport]"][value="1"]');
		$me->checkOption('input[name="transport_and_payment_form[payment]"][value="2"]');
		$me->clickByText('Pokračovat v objednávce');

		$me->fillField('input[name="order_personal_info_form[firstName]"]', 'Jan');
		$me->clickByText('Zpět na výběr dopravy a platby');
		$me->amOnPage('/objednavka/');
		$me->clickByText('Pokračovat v objednávce');

		$me->seeInField('input[name="order_personal_info_form[firstName]"]', 'Jan');
	}

}
