<?php

namespace SS6\ShopBundle\Tests\Unit\Form\Front;

use SS6\ShopBundle\Form\Front\Order\PersonalInfoFormType;
use SS6\ShopBundle\Tests\Test\FunctionalTestCase;
use Symfony\Component\Form\FormFactoryInterface;

class PersonalInfoFormTypeTest extends FunctionalTestCase {

	public function getTermsAndConditionsAgreementIsMandatoryData() {
		return [
			[$this->getPersonalInfoFormData(true), true],
			[$this->getPersonalInfoFormData(false), false],
		];
	}

	/**
	 * @dataProvider getTermsAndConditionsAgreementIsMandatoryData
	 */
	public function testTermsAndConditionsAgreementIsMandatory($personalInfoFormData, $isExpectedValid) {
		$formFactory = $this->getContainer()->get(FormFactoryInterface::class);
		/* @var $formFactory \Symfony\Component\Form\FormFactoryInterface */
		$personalInfoForm = $formFactory->create(new PersonalInfoFormType(), null, ['csrf_protection' => false]);

		$personalInfoForm->submit($personalInfoFormData);

		$this->assertSame($isExpectedValid, $personalInfoForm->isValid());
	}

	/**
	 * @param bool $termsAndConditionsAgreement
	 * @return mixed
	 */
	private function getPersonalInfoFormData($termsAndConditionsAgreement) {
		$personalInfoFormData['firstName'] = 'test';
		$personalInfoFormData['lastName'] = 'test';
		$personalInfoFormData['email'] = 'test@test.cz';
		$personalInfoFormData['telephone'] = '123456789';
		$personalInfoFormData['street'] = 'test';
		$personalInfoFormData['city'] = 'test';
		$personalInfoFormData['postcode'] = '12345';
		$personalInfoFormData['termsAndConditionsAgreement'] = $termsAndConditionsAgreement;

		return $personalInfoFormData;
	}

}
