<?php

namespace SS6\ShopBundle\Tests\Acceptance\acceptance\PageObject\Front;

use SS6\ShopBundle\Tests\Acceptance\acceptance\PageObject\AbstractPage;

class OrderPage extends AbstractPage {

	const FIRST_NAME_FIELD_NAME = 'order_personal_info_form[firstName]';

	/**
	 * @param string $transportTitle
	 */
	public function assertTransportIsNotSelected($transportTitle) {
		$this->tester->dontSeeCheckboxIsCheckedByLabel($transportTitle);
	}

	/**
	 * @param string $transportTitle
	 */
	public function assertTransportIsSelected($transportTitle) {
		$this->tester->seeCheckboxIsCheckedByLabel($transportTitle);
	}

	/**
	 * @param string $transportTitle
	 */
	public function selectTransport($transportTitle) {
		$this->tester->checkOptionByLabel($transportTitle);
	}

	/**
	 * @param string $paymentTitle
	 */
	public function assertPaymentIsNotSelected($paymentTitle) {
		$this->tester->dontSeeCheckboxIsCheckedByLabel($paymentTitle);
	}

	/**
	 * @param string $paymentTitle
	 */
	public function assertPaymentIsSelected($paymentTitle) {
		$this->tester->seeCheckboxIsCheckedByLabel($paymentTitle);
	}

	/**
	 * @param string $paymentTitle
	 */
	public function selectPayment($paymentTitle) {
		$this->tester->checkOptionByLabel($paymentTitle);
	}

	/**
	 * @param string $firstName
	 */
	public function fillFirstName($firstName) {
		$this->tester->fillFieldByName(self::FIRST_NAME_FIELD_NAME, $firstName);
	}

	/**
	 * @param string $firstName
	 */
	public function assertFirstNameIsFilled($firstName) {
		$this->tester->seeInFieldByName($firstName, self::FIRST_NAME_FIELD_NAME);
	}
}
