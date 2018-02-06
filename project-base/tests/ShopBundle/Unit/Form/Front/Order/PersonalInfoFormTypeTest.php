<?php

namespace Tests\ShopBundle\Unit\Form\Front\Order;

use Shopsys\ShopBundle\Form\Front\Order\PersonalInfoFormType;
use Shopsys\ShopBundle\Model\Country\Country;
use Shopsys\ShopBundle\Model\Country\CountryFacade;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

class PersonalInfoFormTypeTest extends TypeTestCase
{
    /**
     * @var CountryFacade
     */
    private $countryFacade;

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
        $personalInfoForm = $this->factory->create(PersonalInfoFormType::class, null, [
            'domain_id' => 1,
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
        $personalInfoFormData = [];
        $personalInfoFormData['firstName'] = 'test';
        $personalInfoFormData['lastName'] = 'test';
        $personalInfoFormData['email'] = 'test@test.cz';
        $personalInfoFormData['telephone'] = '123456789';
        $personalInfoFormData['street'] = 'test';
        $personalInfoFormData['city'] = 'test';
        $personalInfoFormData['postcode'] = '12345';
        $personalInfoFormData['country'] = 1;
        $personalInfoFormData['legalConditionsAgreement'] = $legalConditionsAgreement;
        $personalInfoFormData['newsletterSubscription'] = false;

        return $personalInfoFormData;
    }

    /**
     * @return array
     */
    protected function getExtensions(): array
    {
        return [
            new ValidatorExtension(Validation::createValidator()),
            new PreloadedExtension([new PersonalInfoFormType($this->countryFacade)], []),
        ];
    }

    protected function setUp()
    {
        $countryMock = $this->createMock(Country::class);
        $countryMock->method('getId')->willReturn(1);

        $this->countryFacade = $this->createMock(CountryFacade::class);
        $this->countryFacade->method('getAllByDomainId')->willReturn([$countryMock]);
        parent::setUp();
    }
}
