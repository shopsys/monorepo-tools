<?php

namespace Tests\ShopBundle\Acceptance\acceptance;

use Tests\ShopBundle\Acceptance\acceptance\PageObject\Front\LayoutPage;
use Tests\ShopBundle\Acceptance\acceptance\PageObject\Front\RegistrationPage;
use Tests\ShopBundle\Test\Codeception\AcceptanceTester;

class CustomerRegistrationCest
{
    const MINIMUM_FORM_SUBMIT_WAIT_TIME = 10;

    public function testSuccessfulRegistration(
        RegistrationPage $registrationPage,
        AcceptanceTester $me,
        LayoutPage $layoutPage
    ) {
        $me->wantTo('successfully register new customer');
        $me->amOnPage('/');
        $layoutPage->clickOnRegistration();
        $registrationPage->register('Roman', 'Štěpánek', 'no-reply.16@netdevelo.cz', 'user123', 'user123');
        $me->wait(self::MINIMUM_FORM_SUBMIT_WAIT_TIME);
        $me->see('You have been successfully registered');
        $me->see('Roman Štěpánek');
        $me->see('Log out');
    }

    public function testAlreadyUsedEmail(RegistrationPage $registrationPage, AcceptanceTester $me)
    {
        $me->wantTo('use already used email while registration');
        $me->amOnPage('/registration/');
        $registrationPage->register('Roman', 'Štěpánek', 'no-reply@netdevelo.cz', 'user123', 'user123');
        $registrationPage->seeEmailError('Email no-reply@netdevelo.cz is already registered');
    }

    public function testPasswordMismatch(RegistrationPage $registrationPage, AcceptanceTester $me)
    {
        $me->wantTo('use mismatching passwords while registration');
        $me->amOnPage('/registration/');
        $registrationPage->register('Roman', 'Štěpánek', 'no-reply.16@netdevelo.cz', 'user123', 'missmatchingPassword');
        $registrationPage->seePasswordError('Passwords do not match');
    }
}
