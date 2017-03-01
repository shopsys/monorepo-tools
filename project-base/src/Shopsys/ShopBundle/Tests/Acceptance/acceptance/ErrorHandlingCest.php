<?php

namespace Shopsys\ShopBundle\Tests\Acceptance\acceptance;

use Shopsys\ShopBundle\Tests\Test\Codeception\AcceptanceTester;

class ErrorHandlingCest
{
    public function testDisplayNotice(AcceptanceTester $me)
    {
        $me->wantTo('display notice error page');
        $me->amOnPage('/test/error-handler/notice');
        $me->see('Oops! Error occurred');
        $me->dontSee('Notice');
    }
}
