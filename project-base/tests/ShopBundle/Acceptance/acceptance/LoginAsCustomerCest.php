<?php

namespace Tests\ShopBundle\Acceptance\acceptance;

use Tests\ShopBundle\Acceptance\acceptance\PageObject\Admin\LoginPage;
use Tests\ShopBundle\Test\Codeception\AcceptanceTester;

class LoginAsCustomerCest
{
    public function testLoginAsCustomer(AcceptanceTester $me, LoginPage $loginPage)
    {
        $me->wantTo('login as a customer from admin');
        $loginPage->loginAsAdmin();
        $me->amOnPage('/admin/customer/edit/2');
        $me->clickByText('Log in as user');
        $me->switchToLastOpenedWindow();
        $me->seeCurrentPageEquals('/');
        $me->see('Attention! You are administrator logged in as the customer.');
        $me->see('Igor Anpilogov');
    }
}
