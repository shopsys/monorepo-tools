<?php

namespace SS6\ShopBundle\Tests\Unit\Form\Front;

use SS6\ShopBundle\DataFixtures\Demo\CountryDataFixture;
use SS6\ShopBundle\Form\Front\Order\PersonalInfoFormType;
use SS6\ShopBundle\Tests\Test\DatabaseTestCase;
use Symfony\Component\Form\FormFactoryInterface;

class PersonalInfoFormTypeTest extends DatabaseTestCase {

	/**
	 * @return array
	 */
	public function getTermsAndConditionsAgreementIsMandatoryData() {
		return [
			[$this->getPersonalInfoFormData(true), true],
			[$this->getPersonalInfoFormData(false), false],
		];
	}

	/**
	 * @param array $personalInfoFormData
	 * @param $isExpectedValid
	 * @dataProvider getTermsAndConditionsAgreementIsMandatoryData
	 */
	public function testTermsAndConditionsAgreementIsMandatory(array $personalInfoFormData, $isExpectedValid) {
		$formFactory = $this->getContainer()->get(FormFactoryInterface::class);
		/* @var $formFactory \Symfony\Component\Form\FormFactoryInterface */

		$countries = [
			$this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC_1),
			$this->getReference(CountryDataFixture::COUNTRY_SLOVAKIA_1),
		];

		$personalInfoForm = $formFactory->create(new PersonalInfoFormType($countries), null, ['csrf_protection' => false]);

		$personalInfoForm->submit($personalInfoFormData);

		$this->assertSame($isExpectedValid, $personalInfoForm->isValid());
	}

	/**
	 * @param bool $termsAndConditionsAgreement
	 * @return array
	 */
	private function getPersonalInfoFormData($termsAndConditionsAgreement) {
		$country = $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC_1);
		/* @var $country \SS6\ShopBundle\Model\Country\Country */

		$personalInfoFormData = [];
		$personalInfoFormData['firstName'] = 'test';
		$personalInfoFormData['lastName'] = 'test';
		$personalInfoFormData['email'] = 'test@test.cz';
		$personalInfoFormData['telephone'] = '123456789';
		$personalInfoFormData['street'] = 'test';
		$personalInfoFormData['city'] = 'test';
		$personalInfoFormData['postcode'] = '12345';
		$personalInfoFormData['country'] = $country->getId();
		$personalInfoFormData['termsAndConditionsAgreement'] = $termsAndConditionsAgreement;
		$personalInfoFormData['newsletterSubscription'] = false;

		return $personalInfoFormData;
	}

}
