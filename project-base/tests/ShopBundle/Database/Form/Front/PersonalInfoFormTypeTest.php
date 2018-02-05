<?php

namespace Tests\ShopBundle\Database\Form\Front;

use Shopsys\ShopBundle\DataFixtures\Demo\CountryDataFixture;
use Shopsys\ShopBundle\Form\Front\Order\PersonalInfoFormType;
use Symfony\Component\Form\FormFactoryInterface;
use Tests\ShopBundle\Test\DatabaseTestCase;

class PersonalInfoFormTypeTest extends DatabaseTestCase
{
    /**
     * @return array
     */
    public function getTermsAndConditionsAgreementIsMandatoryData()
    {
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
    public function testTermsAndConditionsAgreementIsMandatory(array $personalInfoFormData, $isExpectedValid)
    {
        $formFactory = $this->getServiceByType(FormFactoryInterface::class);
        /* @var $formFactory \Symfony\Component\Form\FormFactoryInterface */

        $personalInfoForm = $formFactory->create(PersonalInfoFormType::class, null, [
            'domain_id' => 1,
            'csrf_protection' => false,
        ]);

        $personalInfoForm->submit($personalInfoFormData);

        $this->assertSame($isExpectedValid, $personalInfoForm->isValid());
    }

    /**
     * @param bool $legalConditionsAgreement
     * @return array
     */
    private function getPersonalInfoFormData($legalConditionsAgreement)
    {
        $country = $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC_1);
        /* @var $country \Shopsys\ShopBundle\Model\Country\Country */

        $personalInfoFormData = [];
        $personalInfoFormData['firstName'] = 'test';
        $personalInfoFormData['lastName'] = 'test';
        $personalInfoFormData['email'] = 'test@test.cz';
        $personalInfoFormData['telephone'] = '123456789';
        $personalInfoFormData['street'] = 'test';
        $personalInfoFormData['city'] = 'test';
        $personalInfoFormData['postcode'] = '12345';
        $personalInfoFormData['country'] = $country->getId();
        $personalInfoFormData['termsAndConditionsAgreement'] = $legalConditionsAgreement;
        $personalInfoFormData['newsletterSubscription'] = false;

        return $personalInfoFormData;
    }
}
