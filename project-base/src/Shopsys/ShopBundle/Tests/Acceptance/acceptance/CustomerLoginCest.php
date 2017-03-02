<?php

namespace Shopsys\ShopBundle\Tests\Acceptance\acceptance;

use Shopsys\ShopBundle\Tests\Acceptance\acceptance\PageObject\Front\LayoutPage;
use Shopsys\ShopBundle\Tests\Acceptance\acceptance\PageObject\Front\LoginPage;
use Shopsys\ShopBundle\Tests\Test\Codeception\AcceptanceTester;

class CustomerLoginCest
{
    public function testLoginAsCustomerFromMainPage(
        LoginPage $loginPage,
        AcceptanceTester $me,
        LayoutPage $layoutPage
    ) {
        $me->wantTo('login as a customer from main page');
        $me->amOnPage('/');
        $layoutPage->openLoginPopup();
        $loginPage->login('no-reply@netdevelo.cz', 'user123');
        $me->see('Jaromír Jágr');
        $me->clickByText('Log out');
        $me->see('Log in');
        $me->seeCurrentPageEquals('/');
    }

    public function testLoginAsCustomerFromCategoryPage(
        LoginPage $loginPage,
        AcceptanceTester $me,
        LayoutPage $layoutPage
    ) {
        $me->wantTo('login as a customer from category page');
        $me->amOnPage('/personal-computers-accessories/');
        $layoutPage->openLoginPopup();
        $loginPage->login('no-reply@netdevelo.cz', 'user123');
        $me->see('Jaromír Jágr');
        $me->clickByText('Log out');
        $me->see('Log in');
        $me->seeCurrentPageEquals('/');
    }

    public function testLoginAsCustomerFromLoginPage(LoginPage $loginPage, AcceptanceTester $me)
    {
        $me->wantTo('login as a customer from login page');
        $me->amOnPage('/login/');
        $loginPage->login('no-reply@netdevelo.cz', 'user123');
        $me->see('Jaromír Jágr');
        $me->clickByText('Log out');
        $me->see('Log in');
        $me->seeCurrentPageEquals('/');
    }
}
