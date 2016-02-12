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

		$me->dontSeeCheckboxIsCheckedById('transport_and_payment_form_transport_0');
		$me->checkOptionById('transport_and_payment_form_transport_0');
		$me->dontSeeCheckboxIsCheckedById('transport_and_payment_form_payment_1');
		$me->checkOptionById('transport_and_payment_form_payment_1');
		$me->clickByText('Pokračovat v objednávce');
		$me->clickByText('Zpět na výběr dopravy a platby');

		$me->seeCheckboxIsCheckedById('transport_and_payment_form_transport_0');
		$me->seeCheckboxIsCheckedById('transport_and_payment_form_payment_1');
	}

	public function testFormRemembersPaymentAndTransportWhenGoingDirectlyToUrl(AcceptanceTester $me) {
		$me->wantTo('have my payment and transport remebered by order');

		$me->amOnPage('/televize-audio/');
		$me->clickByText('Vložit do košíku');
		$me->waitForAjax();
		$me->clickByText('Přejít do košíku');
		$me->clickByText('Objednat');

		$me->dontSeeCheckboxIsCheckedById('transport_and_payment_form_transport_0');
		$me->checkOptionById('transport_and_payment_form_transport_0');
		$me->dontSeeCheckboxIsCheckedById('transport_and_payment_form_payment_1');
		$me->checkOptionById('transport_and_payment_form_payment_1');
		$me->clickByText('Pokračovat v objednávce');
		$me->amOnPage('/objednavka/');

		$me->seeCheckboxIsCheckedById('transport_and_payment_form_transport_0');
		$me->seeCheckboxIsCheckedById('transport_and_payment_form_payment_1');
	}

	public function testFormRemembersFirstName(AcceptanceTester $me) {
		$me->wantTo('have my first name remebered by order');

		$me->amOnPage('/televize-audio/');
		$me->clickByText('Vložit do košíku');
		$me->waitForAjax();
		$me->clickByText('Přejít do košíku');
		$me->clickByText('Objednat');
		$me->checkOptionById('transport_and_payment_form_transport_0');
		$me->checkOptionById('transport_and_payment_form_payment_1');
		$me->clickByText('Pokračovat v objednávce');

		$me->fillFieldByName('order_personal_info_form[firstName]', 'Jan');
		$me->clickByText('Zpět na výběr dopravy a platby');
		$me->amOnPage('/objednavka/');
		$me->clickByText('Pokračovat v objednávce');

		$me->seeInField('input[name="order_personal_info_form[firstName]"]', 'Jan');
	}

}
