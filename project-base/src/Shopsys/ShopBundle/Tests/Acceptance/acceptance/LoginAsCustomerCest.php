<?php

namespace Shopsys\ShopBundle\Tests\Acceptance\acceptance;

use Shopsys\ShopBundle\Tests\Acceptance\acceptance\PageObject\Admin\LoginPage;
use Shopsys\ShopBundle\Tests\Test\Codeception\AcceptanceTester;

class LoginAsCustomerCest
{
    public function testLoginAsCustomer(AcceptanceTester $me, LoginPage $loginPage)
    {
        $me->wantTo('login as a customer from admin');
        $loginPage->login(LoginPage::ADMIN_USERNAME, LoginPage::ADMIN_PASSWORD);
        $me->amOnPage('/admin/customer/edit/2');
        $me->clickByText('Log in as user');
        $me->switchToLastOpenedWindow();
        $me->seeCurrentPageEquals('/');
        $me->see('Attention! You are administrator logged in as the customer.');
        $me->see('Igor Anpilogov');
    }
}
