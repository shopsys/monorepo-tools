<?php

namespace Tests\ShopBundle\Acceptance\acceptance;

use Tests\ShopBundle\Acceptance\acceptance\PageObject\Admin\LoginPage;
use Tests\ShopBundle\Test\Codeception\AcceptanceTester;

class AdministratorLoginCest
{
    public function testSuccessfulLogin(AcceptanceTester $me, LoginPage $loginPage)
    {
        $me->wantTo('login on admin with valid data');
        $loginPage->loginAsAdmin();
    }

    public function testLoginWithInvalidUsername(AcceptanceTester $me, LoginPage $loginPage)
    {
        $me->wantTo('login on admin with nonexistent username');
        $loginPage->login('nonexistent username', LoginPage::ADMIN_PASSWORD);
        $loginPage->assertLoginFailed();
    }

    public function testLoginWithInvalidPassword(AcceptanceTester $me, LoginPage $loginPage)
    {
        $me->wantTo('login on admin with invalid password');
        $loginPage->login(LoginPage::ADMIN_USERNAME, 'invalid password');
        $loginPage->assertLoginFailed();
    }
}
