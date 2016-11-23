<?php

namespace SS6\ShopBundle\Tests\Acceptance\acceptance;

use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Model\Script\ScriptFacade;
use SS6\ShopBundle\Tests\Acceptance\acceptance\PageObject\Front\OrderPage;
use SS6\ShopBundle\Tests\Acceptance\acceptance\PageObject\Front\ProductListPage;
use SS6\ShopBundle\Tests\Test\Codeception\AcceptanceTester;
use SS6\ShopBundle\Tests\Test\Codeception\Helper\SymfonyHelper;

class OrderCest {

	public function testFormRemembersPaymentAndTransportWhenClickingBack(
		ProductListPage $productListPage,
		OrderPage $orderPage,
		AcceptanceTester $me
	) {
		$me->wantTo('have my payment and transport remebered by order');

		$me->amOnPage('/televize-audio/');
		$productListPage->addProductToCartByName('Defender 2.0 SPK-480');
		$me->clickByText('Přejít do košíku');
		$me->clickByText('Objednat');

		$orderPage->assertTransportIsNotSelected('Česká pošta - balík do ruky');
		$orderPage->selectTransport('Česká pošta - balík do ruky');
		$orderPage->assertPaymentIsNotSelected('Dobírka');
		$orderPage->selectPayment('Dobírka');
		$me->waitForAjax();
		$me->clickByText('Pokračovat v objednávce');
		$me->clickByText('Zpět na výběr dopravy a platby');

		$orderPage->assertTransportIsSelected('Česká pošta - balík do ruky');
		$orderPage->assertPaymentIsSelected('Dobírka');
	}

	public function testFormRemembersPaymentAndTransportWhenGoingDirectlyToUrl(
		ProductListPage $productListPage,
		OrderPage $orderPage,
		AcceptanceTester $me
	) {
		$me->wantTo('have my payment and transport remebered by order');

		$me->amOnPage('/televize-audio/');
		$productListPage->addProductToCartByName('Defender 2.0 SPK-480');
		$me->clickByText('Přejít do košíku');
		$me->clickByText('Objednat');

		$orderPage->assertTransportIsNotSelected('Česká pošta - balík do ruky');
		$orderPage->selectTransport('Česká pošta - balík do ruky');
		$orderPage->assertPaymentIsNotSelected('Dobírka');
		$orderPage->selectPayment('Dobírka');
		$me->waitForAjax();
		$me->clickByText('Pokračovat v objednávce');
		$me->amOnPage('/objednavka/');

		$orderPage->assertTransportIsSelected('Česká pošta - balík do ruky');
		$orderPage->assertPaymentIsSelected('Dobírka');
	}

	public function testFormRemembersFirstName(ProductListPage $productListPage, OrderPage $orderPage, AcceptanceTester $me) {
		$me->wantTo('have my first name remebered by order');

		$me->amOnPage('/televize-audio/');
		$productListPage->addProductToCartByName('Defender 2.0 SPK-480');
		$me->clickByText('Přejít do košíku');
		$me->clickByText('Objednat');
		$orderPage->selectTransport('Česká pošta - balík do ruky');
		$orderPage->selectPayment('Dobírka');
		$me->waitForAjax();
		$me->clickByText('Pokračovat v objednávce');

		$orderPage->fillFirstName('Jan');
		$me->clickByText('Zpět na výběr dopravy a platby');
		$me->amOnPage('/objednavka/');
		$me->clickByText('Pokračovat v objednávce');

		$orderPage->assertFirstNameIsFilled('Jan');
	}

	public function testOrderCanBeCompletedAndHasGoogleAnalyticsTrackingIdInSource(
		ProductListPage $productListPage,
		OrderPage $orderPage,
		AcceptanceTester $me,
		SymfonyHelper $symfonyHelper
	) {
		$this->setGoogleAnalyticsTrackingId('GA-test', $symfonyHelper->grabServiceFromContainer(ScriptFacade::class));

		$this->testOrderCanBeCompleted($productListPage, $orderPage, $me);

		$me->seeInSource('GA-test');
	}

	/**
	 * @param string $trackingId
	 * @param \SS6\ShopBundle\Model\Script\ScriptFacade $scriptFacade
	 */
	private function setGoogleAnalyticsTrackingId($trackingId, ScriptFacade $scriptFacade) {
		$scriptFacade->setGoogleAnalyticsTrackingId($trackingId, Domain::FIRST_DOMAIN_ID);
	}

	private function testOrderCanBeCompleted(
		ProductListPage $productListPage,
		OrderPage $orderPage,
		AcceptanceTester $me
	) {
		$me->amOnPage('/televize-audio/');
		$productListPage->addProductToCartByName('Defender 2.0 SPK-480');
		$me->clickByText('Přejít do košíku');
		$me->clickByText('Objednat');

		$orderPage->selectTransport('Česká pošta - balík do ruky');
		$orderPage->selectPayment('Dobírka');
		$me->waitForAjax();
		$me->clickByText('Pokračovat v objednávce');

		$orderPage->fillPersonalInfo('Karel', 'Novák', 'no-reply@shopsys.com', '123456789');
		$orderPage->fillBillingAddress('Koksární 10', 'Ostrava', '702 00');
		$orderPage->acceptTermsAndConditions();

		$me->clickByText('Dokončit objednávku');

		$me->see('Objednávka byla odeslána');
	}

}
