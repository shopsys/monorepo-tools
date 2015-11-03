<?php

namespace SS6\ShopBundle\Tests\Acceptance\acceptance;

use SS6\ShopBundle\Tests\Test\Codeception\AcceptanceTester;

class OrderCest {

	public function testFormRemembersPaymentAndTransportWhenClickingBack(AcceptanceTester $me) {
		$me->wantTo('have my payment and transport remebered by order');

		$me->amOnPage('/televize-audio/');
		$me->click('Vložit do košíku');
		$me->waitForAjax();
		$me->click('Přejít do košíku');
		$me->click('Objednat');

		$me->dontSeeCheckboxIsChecked('input[name="transportAndPayment_form[transport]"][value="1"]');
		$me->checkOption('input[name="transportAndPayment_form[transport]"][value="1"]');
		$me->dontSeeCheckboxIsChecked('input[name="transportAndPayment_form[payment]"][value="2"]');
		$me->checkOption('input[name="transportAndPayment_form[payment]"][value="2"]');
		$me->click('Pokračovat v objednávce');
		$me->click('Zpět na výběr dopravy a platby');

		$me->seeCheckboxIsChecked('input[name="transportAndPayment_form[transport]"][value="1"]');
		$me->seeCheckboxIsChecked('input[name="transportAndPayment_form[payment]"][value="2"]');
	}

	public function testFormRemembersPaymentAndTransportWhenGoingDirectlyToUrl(AcceptanceTester $me) {
		$me->wantTo('have my payment and transport remebered by order');

		$me->amOnPage('/televize-audio/');
		$me->click('Vložit do košíku');
		$me->waitForAjax();
		$me->click('Přejít do košíku');
		$me->click('Objednat');

		$me->dontSeeCheckboxIsChecked('input[name="transportAndPayment_form[transport]"][value="1"]');
		$me->checkOption('input[name="transportAndPayment_form[transport]"][value="1"]');
		$me->dontSeeCheckboxIsChecked('input[name="transportAndPayment_form[payment]"][value="2"]');
		$me->checkOption('input[name="transportAndPayment_form[payment]"][value="2"]');
		$me->click('Pokračovat v objednávce');
		$me->amOnPage('/objednavka/');

		$me->seeCheckboxIsChecked('input[name="transportAndPayment_form[transport]"][value="1"]');
		$me->seeCheckboxIsChecked('input[name="transportAndPayment_form[payment]"][value="2"]');
	}

	public function testFormRemembersFirstName(AcceptanceTester $me) {
		$me->wantTo('have my first name remebered by order');

		$me->amOnPage('/televize-audio/');
		$me->click('Vložit do košíku');
		$me->waitForAjax();
		$me->click('Přejít do košíku');
		$me->click('Objednat');
		$me->checkOption('input[name="transportAndPayment_form[transport]"][value="1"]');
		$me->checkOption('input[name="transportAndPayment_form[payment]"][value="2"]');
		$me->click('Pokračovat v objednávce');

		$me->fillField('input[name="orderPersonalInfo_form[firstName]"]', 'Jan');
		$me->click('Zpět na výběr dopravy a platby');
		$me->amOnPage('/objednavka/');
		$me->click('Pokračovat v objednávce');

		$me->seeInField('input[name="orderPersonalInfo_form[firstName]"]', 'Jan');
	}

}
