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
        $me->clickByText('Odhlásit se');
        $me->see('Přihlásit se');
        $me->seeCurrentPageEquals('/');
    }

    public function testLoginAsCustomerFromCategoryPage(
        LoginPage $loginPage,
        AcceptanceTester $me,
        LayoutPage $layoutPage
    ) {
        $me->wantTo('login as a customer from category page');
        $me->amOnPage('/pocitace-prislusenstvi/');
        $layoutPage->openLoginPopup();
        $loginPage->login('no-reply@netdevelo.cz', 'user123');
        $me->see('Jaromír Jágr');
        $me->clickByText('Odhlásit se');
        $me->see('Přihlásit se');
        $me->seeCurrentPageEquals('/');
    }

    public function testLoginAsCustomerFromLoginPage(LoginPage $loginPage, AcceptanceTester $me) {
        $me->wantTo('login as a customer from login page');
        $me->amOnPage('/prihlaseni/');
        $loginPage->login('no-reply@netdevelo.cz', 'user123');
        $me->see('Jaromír Jágr');
        $me->clickByText('Odhlásit se');
        $me->see('Přihlásit se');
        $me->seeCurrentPageEquals('/');
    }
}
