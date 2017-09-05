<?php

namespace Tests\ShopBundle\Acceptance\acceptance;

use Tests\ShopBundle\Acceptance\acceptance\PageObject\Front\LayoutPage;
use Tests\ShopBundle\Acceptance\acceptance\PageObject\Front\LoginPage;
use Tests\ShopBundle\Test\Codeception\AcceptanceTester;

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
        $layoutPage->logout();
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
        $layoutPage->logout();
        $me->see('Log in');
        $me->seeCurrentPageEquals('/');
    }

    public function testLoginAsCustomerFromLoginPage(
        LoginPage $loginPage,
        AcceptanceTester $me,
        LayoutPage $layoutPage
    ) {
        $me->wantTo('login as a customer from login page');
        $me->amOnPage('/login/');
        $loginPage->login('no-reply@netdevelo.cz', 'user123');
        $me->see('Jaromír Jágr');
        $layoutPage->logout();
        $me->see('Log in');
        $me->seeCurrentPageEquals('/');
    }
}
