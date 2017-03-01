<?php

namespace Shopsys\ShopBundle\Tests\Acceptance\acceptance\PageObject\Front;

use Shopsys\ShopBundle\Tests\Acceptance\acceptance\PageObject\AbstractPage;

class OrderPage extends AbstractPage
{
    const FIRST_NAME_FIELD_NAME = 'order_personal_info_form[firstName]';

    /**
     * @param string $transportTitle
     */
    public function assertTransportIsNotSelected($transportTitle)
    {
        $this->tester->dontSeeCheckboxIsCheckedByLabel($transportTitle);
    }

    /**
     * @param string $transportTitle
     */
    public function assertTransportIsSelected($transportTitle)
    {
        $this->tester->seeCheckboxIsCheckedByLabel($transportTitle);
    }

    /**
     * @param string $transportTitle
     */
    public function selectTransport($transportTitle)
    {
        $this->tester->checkOptionByLabel($transportTitle);
    }

    /**
     * @param string $paymentTitle
     */
    public function assertPaymentIsNotSelected($paymentTitle)
    {
        $this->tester->dontSeeCheckboxIsCheckedByLabel($paymentTitle);
    }

    /**
     * @param string $paymentTitle
     */
    public function assertPaymentIsSelected($paymentTitle)
    {
        $this->tester->seeCheckboxIsCheckedByLabel($paymentTitle);
    }

    /**
     * @param string $paymentTitle
     */
    public function selectPayment($paymentTitle)
    {
        $this->tester->checkOptionByLabel($paymentTitle);
    }

    /**
     * @param string $firstName
     */
    public function fillFirstName($firstName)
    {
        $this->tester->fillFieldByName(self::FIRST_NAME_FIELD_NAME, $firstName);
    }

    /**
     * @param string $firstName
     */
    public function assertFirstNameIsFilled($firstName)
    {
        $this->tester->seeInFieldByName($firstName, self::FIRST_NAME_FIELD_NAME);
    }

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $telephone
     */
    public function fillPersonalInfo($firstName, $lastName, $email, $telephone)
    {
        $this->fillFirstName($firstName);
        $this->tester->fillFieldByName('order_personal_info_form[lastName]', $lastName);
        $this->tester->fillFieldByName('order_personal_info_form[email]', $email);
        $this->tester->fillFieldByName('order_personal_info_form[telephone]', $telephone);
    }

    /**
     * @param string $street
     * @param string $city
     * @param string $postcode
     */
    public function fillBillingAddress($street, $city, $postcode)
    {
        $this->tester->fillFieldByName('order_personal_info_form[street]', $street);
        $this->tester->fillFieldByName('order_personal_info_form[city]', $city);
        $this->tester->fillFieldByName('order_personal_info_form[postcode]', $postcode);
    }

    /**
     * @param string $note
     */
    public function fillNote($note)
    {
        $this->tester->fillFieldByName('order_personal_info_form[note]', $note);
    }

    public function acceptTermsAndConditions()
    {
        $this->tester->checkOptionByLabel('I agree with terms and conditons');
    }
}
