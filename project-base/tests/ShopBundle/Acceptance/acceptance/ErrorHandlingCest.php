<?php

namespace Tests\ShopBundle\Acceptance\acceptance;

use Tests\ShopBundle\Test\Codeception\AcceptanceTester;

class ErrorHandlingCest
{
    /**
     * @param \Tests\ShopBundle\Test\Codeception\AcceptanceTester $me
     */
    public function testDisplayNotice(AcceptanceTester $me)
    {
        $me->wantTo('display notice error page');
        $me->amOnPage('/test/error-handler/notice');
        $me->see('Oops! Error occurred');
        $me->dontSee('Notice');
    }

    /**
     * @param \Tests\ShopBundle\Test\Codeception\AcceptanceTester $me
     */
    public function testAccessUnknownDomain(AcceptanceTester $me)
    {
        $me->wantTo('display error when accessing an unknown domain');
        $me->amOnPage('/test/error-handler/unknown-domain');
        $me->see('You are trying to access an unknown domain');
        $me->dontSee('Page not found!');
    }
}
