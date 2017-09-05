<?php

namespace Tests\ShopBundle\Acceptance\acceptance;

use Tests\ShopBundle\Acceptance\acceptance\PageObject\Front\LayoutPage;
use Tests\ShopBundle\Acceptance\acceptance\PageObject\Front\RegistrationPage;
use Tests\ShopBundle\Test\Codeception\AcceptanceTester;

class CustomerRegistrationCest
{
    public function testSuccessfulRegistration(
        RegistrationPage $registrationPage,
        AcceptanceTester $me,
        LayoutPage $layoutPage
    ) {
        $me->wantTo('successfully register new customer');
        $me->amOnPage('/');
        $layoutPage->clickOnRegistration();
        $registrationPage->register('Roman', 'Štěpánek', 'no-reply.16@netdevelo.cz', 'user123', 'user123');
        $me->see('You have been successfully registered');
        $me->see('Roman Štěpánek');
        $me->see('Log out');
    }

    public function testAlreadyUsedEmail(RegistrationPage $registrationPage, AcceptanceTester $me)
    {
        $me->wantTo('use already used email while registration');
        $me->amOnPage('/registration/');
        $registrationPage->register('Roman', 'Štěpánek', 'no-reply@netdevelo.cz', 'user123', 'user123');
        $registrationPage->seeEmailError('There is already a customer with this e-mail in the database');
    }

    public function testPasswordMismatch(RegistrationPage $registrationPage, AcceptanceTester $me)
    {
        $me->wantTo('use mismatching passwords while registration');
        $me->amOnPage('/registration/');
        $registrationPage->register('Roman', 'Štěpánek', 'no-reply.16@netdevelo.cz', 'user123', 'missmatchingPassword');
        $registrationPage->seePasswordError('Passwords do not match');
    }
}
