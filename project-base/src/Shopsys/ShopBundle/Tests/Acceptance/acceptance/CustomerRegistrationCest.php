<?php

namespace Shopsys\ShopBundle\Tests\Acceptance\acceptance;

use Shopsys\ShopBundle\Tests\Acceptance\acceptance\PageObject\Front\RegistrationPage;
use Shopsys\ShopBundle\Tests\Test\Codeception\AcceptanceTester;

class CustomerRegistrationCest
{
    public function testSuccessfulRegistration(RegistrationPage $registrationPage, AcceptanceTester $me)
    {
        $me->wantTo('successfully register new customer');
        $me->amOnPage('/');
        $me->clickByText('Registrace');
        $registrationPage->register('Roman', 'Štěpánek', 'no-reply.16@netdevelo.cz', 'user123', 'user123');
        $me->see('Byli jste úspěšně zaregistrováni');
        $me->see('Roman Štěpánek');
        $me->see('Odhlásit se');
    }

    public function testAlreadyUsedEmail(RegistrationPage $registrationPage, AcceptanceTester $me)
    {
        $me->wantTo('use already used email while registration');
        $me->amOnPage('/registrace/');
        $registrationPage->register('Roman', 'Štěpánek', 'no-reply@netdevelo.cz', 'user123', 'user123');
        $registrationPage->seeEmailError('V databázi se již nachází zákazník s tímto e-mailem');
    }

    public function testPasswordMismatch(RegistrationPage $registrationPage, AcceptanceTester $me)
    {
        $me->wantTo('use mismatching passwords while registration');
        $me->amOnPage('/registrace/');
        $registrationPage->register('Roman', 'Štěpánek', 'no-reply.16@netdevelo.cz', 'user123', 'missmatchingPassword');
        $registrationPage->seePasswordError('Hesla se neshodují');
    }
}
