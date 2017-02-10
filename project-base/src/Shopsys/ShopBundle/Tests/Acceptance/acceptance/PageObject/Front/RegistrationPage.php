<?php

namespace Shopsys\ShopBundle\Tests\Acceptance\acceptance\PageObject\Front;

use Shopsys\ShopBundle\Tests\Acceptance\acceptance\PageObject\AbstractPage;

class RegistrationPage extends AbstractPage
{
    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $firstPassword
     * @param string $secondPassword
     */
    public function register($firstName, $lastName, $email, $firstPassword, $secondPassword)
    {
        $this->tester->fillFieldByName('registration_form[firstName]', $firstName);
        $this->tester->fillFieldByName('registration_form[lastName]', $lastName);
        $this->tester->fillFieldByName('registration_form[email]', $email);
        $this->tester->fillFieldByName('registration_form[password][first]', $firstPassword);
        $this->tester->fillFieldByName('registration_form[password][second]', $secondPassword);
        $this->tester->wait(5);
        $this->tester->clickByName('registration_form[save]');
    }

    /**
     * @param string $text
     */
    public function seeEmailError($text)
    {
        $this->seeErrorForField('.js-validation-error-list-registration_form_email', $text);
    }

    /**
     * @param string $text
     */
    public function seePasswordError($text)
    {
        $this->seeErrorForField('.js-validation-error-list-registration_form_password_first', $text);
    }

    /**
     * @param $fieldClass $text
     * @param string $text
     */
    private function seeErrorForField($fieldClass, $text)
    {
        // Error message might be in popup - wait for animation
        $this->tester->wait(1);
        // Error message might be in fancy title - hover over field
        $this->tester->moveMouseOverByCss($fieldClass);

        $this->tester->see($text);
    }
}
