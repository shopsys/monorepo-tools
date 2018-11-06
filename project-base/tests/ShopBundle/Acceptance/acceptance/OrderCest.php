<?php

namespace Tests\ShopBundle\Acceptance\acceptance;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Script\ScriptFacade;
use Tests\ShopBundle\Acceptance\acceptance\PageObject\Front\OrderPage;
use Tests\ShopBundle\Acceptance\acceptance\PageObject\Front\ProductListPage;
use Tests\ShopBundle\Test\Codeception\AcceptanceTester;
use Tests\ShopBundle\Test\Codeception\Helper\SymfonyHelper;

class OrderCest
{
    /**
     * @param \Tests\ShopBundle\Acceptance\acceptance\PageObject\Front\ProductListPage $productListPage
     * @param \Tests\ShopBundle\Acceptance\acceptance\PageObject\Front\OrderPage $orderPage
     * @param \Tests\ShopBundle\Test\Codeception\AcceptanceTester $me
     */
    public function testFormRemembersPaymentAndTransportWhenClickingBack(
        ProductListPage $productListPage,
        OrderPage $orderPage,
        AcceptanceTester $me
    ) {
        $me->wantTo('have my payment and transport remembered by order');

        $me->amOnPage('/tv-audio/');
        $productListPage->addProductToCartByName('Defender 2.0 SPK-480');
        $me->clickByText('Go to cart');
        $me->clickByText('Order');

        $orderPage->assertTransportIsNotSelected('Czech post');
        $orderPage->selectTransport('Czech post');
        $orderPage->assertPaymentIsNotSelected('Cash on delivery');
        $orderPage->selectPayment('Cash on delivery');
        $me->waitForAjax();
        $me->clickByText('Continue in order');
        $me->clickByText('Back to shipping and payment selection');

        $orderPage->assertTransportIsSelected('Czech post');
        $orderPage->assertPaymentIsSelected('Cash on delivery');
    }

    /**
     * @param \Tests\ShopBundle\Acceptance\acceptance\PageObject\Front\ProductListPage $productListPage
     * @param \Tests\ShopBundle\Acceptance\acceptance\PageObject\Front\OrderPage $orderPage
     * @param \Tests\ShopBundle\Test\Codeception\AcceptanceTester $me
     */
    public function testFormRemembersPaymentAndTransportWhenGoingDirectlyToUrl(
        ProductListPage $productListPage,
        OrderPage $orderPage,
        AcceptanceTester $me
    ) {
        $me->wantTo('have my payment and transport remembered by order');

        $me->amOnPage('/tv-audio/');
        $productListPage->addProductToCartByName('Defender 2.0 SPK-480');
        $me->clickByText('Go to cart');
        $me->clickByText('Order');

        $orderPage->assertTransportIsNotSelected('Czech post');
        $orderPage->selectTransport('Czech post');
        $orderPage->assertPaymentIsNotSelected('Cash on delivery');
        $orderPage->selectPayment('Cash on delivery');
        $me->waitForAjax();
        $me->clickByText('Continue in order');
        $me->amOnPage('/order/');

        $orderPage->assertTransportIsSelected('Czech post');
        $orderPage->assertPaymentIsSelected('Cash on delivery');
    }

    /**
     * @param \Tests\ShopBundle\Acceptance\acceptance\PageObject\Front\ProductListPage $productListPage
     * @param \Tests\ShopBundle\Acceptance\acceptance\PageObject\Front\OrderPage $orderPage
     * @param \Tests\ShopBundle\Test\Codeception\AcceptanceTester $me
     */
    public function testFormRemembersFirstName(ProductListPage $productListPage, OrderPage $orderPage, AcceptanceTester $me)
    {
        $me->wantTo('have my first name remembered by order');

        $me->amOnPage('/tv-audio/');
        $productListPage->addProductToCartByName('Defender 2.0 SPK-480');
        $me->clickByText('Go to cart');
        $me->clickByText('Order');
        $orderPage->selectTransport('Czech post');
        $orderPage->selectPayment('Cash on delivery');
        $me->waitForAjax();
        $me->clickByText('Continue in order');

        $orderPage->fillFirstName('Jan');
        $me->clickByText('Back to shipping and payment selection');
        $me->amOnPage('/order/');
        $me->clickByText('Continue in order');

        $orderPage->assertFirstNameIsFilled('Jan');
    }

    /**
     * @param \Tests\ShopBundle\Acceptance\acceptance\PageObject\Front\ProductListPage $productListPage
     * @param \Tests\ShopBundle\Acceptance\acceptance\PageObject\Front\OrderPage $orderPage
     * @param \Tests\ShopBundle\Test\Codeception\AcceptanceTester $me
     * @param \Tests\ShopBundle\Test\Codeception\Helper\SymfonyHelper $symfonyHelper
     */
    public function testOrderCanBeCompletedAndHasGoogleAnalyticsTrackingIdInSource(
        ProductListPage $productListPage,
        OrderPage $orderPage,
        AcceptanceTester $me,
        SymfonyHelper $symfonyHelper
    ) {
        $scriptFacade = $symfonyHelper->grabServiceFromContainer(ScriptFacade::class);
        $this->setGoogleAnalyticsTrackingId('GA-test', $scriptFacade);

        $this->testOrderCanBeCompleted($productListPage, $orderPage, $me);

        $me->seeInSource('GA-test');
    }

    /**
     * @param string $trackingId
     * @param \Shopsys\FrameworkBundle\Model\Script\ScriptFacade $scriptFacade
     */
    private function setGoogleAnalyticsTrackingId($trackingId, ScriptFacade $scriptFacade)
    {
        $scriptFacade->setGoogleAnalyticsTrackingId($trackingId, Domain::FIRST_DOMAIN_ID);
    }

    /**
     * @param \Tests\ShopBundle\Acceptance\acceptance\PageObject\Front\ProductListPage $productListPage
     * @param \Tests\ShopBundle\Acceptance\acceptance\PageObject\Front\OrderPage $orderPage
     * @param \Tests\ShopBundle\Test\Codeception\AcceptanceTester $me
     */
    private function testOrderCanBeCompleted(
        ProductListPage $productListPage,
        OrderPage $orderPage,
        AcceptanceTester $me
    ) {
        $me->amOnPage('/tv-audio/');
        $productListPage->addProductToCartByName('Defender 2.0 SPK-480');
        $me->clickByText('Go to cart');
        $me->clickByText('Order');

        $orderPage->selectTransport('Czech post');
        $orderPage->selectPayment('Cash on delivery');
        $me->waitForAjax();
        $me->clickByText('Continue in order');

        $orderPage->fillPersonalInfo('Karel', 'Novák', 'no-reply@shopsys.com', '123456789');
        $orderPage->fillBillingAddress('Koksární 10', 'Ostrava', '702 00');
        $orderPage->acceptLegalConditions();

        $me->clickByText('Finish the order');

        $me->see('Order sent');
    }
}
