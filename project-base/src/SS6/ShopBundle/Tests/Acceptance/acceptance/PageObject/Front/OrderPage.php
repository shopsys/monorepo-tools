<?php

namespace SS6\ShopBundle\Tests\Acceptance\acceptance\PageObject\Front;

use SS6\ShopBundle\Tests\Acceptance\acceptance\PageObject\AbstractPage;

class OrderPage extends AbstractPage {

	const CZECH_POST_CHECKBOX_ID = 'transport_and_payment_form_transport_0';
	const CASH_ON_DELIVERY_CHECKBOX_ID = 'transport_and_payment_form_payment_1';
	const FIRST_NAME_FIELD_NAME = 'order_personal_info_form[firstName]';

	public function assertCzechPostTransportIsNotSelected() {
		$this->tester->dontSeeCheckboxIsCheckedById(self::CZECH_POST_CHECKBOX_ID);
	}

	public function assertCzechPostTransportIsSelected() {
		$this->tester->seeCheckboxIsCheckedById(self::CZECH_POST_CHECKBOX_ID);
	}

	public function selectCzechPostTransport() {
		$this->tester->checkOptionById(self::CZECH_POST_CHECKBOX_ID);
	}

	public function assertCashOnDeliveryPaymentIsNotSelected() {
		$this->tester->dontSeeCheckboxIsCheckedById(self::CASH_ON_DELIVERY_CHECKBOX_ID);
	}

	public function assertCashOnDeliveryPaymentIsSelected() {
		$this->tester->seeCheckboxIsCheckedById(self::CASH_ON_DELIVERY_CHECKBOX_ID);
	}

	public function selectCashOnDeliveryPayment() {
		$this->tester->checkOptionById(self::CASH_ON_DELIVERY_CHECKBOX_ID);
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
