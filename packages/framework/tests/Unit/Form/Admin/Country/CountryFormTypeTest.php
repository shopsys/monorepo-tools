<?php

declare(strict_types=1);

namespace Tests\FrameworkBundle\Unit\Form\Admin\Country;

use ReflectionClass;
use Shopsys\FormTypesBundle\MultidomainType;
use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Translation\Translator;
use Shopsys\FrameworkBundle\Form\Admin\Country\CountryFormType;
use Shopsys\FrameworkBundle\Form\DomainsType;
use Shopsys\FrameworkBundle\Form\Locale\LocalizedType;
use Shopsys\FrameworkBundle\Model\Country\Country;
use Shopsys\FrameworkBundle\Model\Country\CountryData;
use Shopsys\FrameworkBundle\Model\Country\CountryFacade;
use Shopsys\FrameworkBundle\Model\Localization\Localization;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

class CountryFormTypeTest extends TypeTestCase
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Localization\Localization
     */
    private $localization;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Country\CountryFacade
     */
    private $countryFacade;

    /**
     * @return array
     */
    private function getFullCountryFormData(): array
    {
        return [
            'save' => '',
            'names' => [
                'en' => 'Czech republic',
                'cs' => 'Česká republika',
            ],
            'code' => 'CZ',
            'enabled' => [
                1 => '1',
                2 => '1',
            ],
            'priority' => [
                1 => '0',
                2 => '0',
            ],
        ];
    }

    public function testNameIsMandatory(): void
    {
        $countryFormData = $this->getFullCountryFormData();

        $countryForm = $this->createCountryForm();
        $countryForm->submit($countryFormData);
        $this->assertTrue($countryForm->isValid());

        $countryFormData['names']['cs'] = '';

        $countryForm = $this->createCountryForm();
        $countryForm->submit($countryFormData);
        $this->assertFalse($countryForm->isValid());
    }

    public function testPriorityIsNumber(): void
    {
        $countryFormData = $this->getFullCountryFormData();

        $countryFormData['priority'][1] = 'asd';

        $countryForm = $this->createCountryForm();
        $countryForm->submit($countryFormData);
        $this->assertFalse($countryForm->isValid(), 'Invalid form');

        $countryFormData['priority'][1] = '1';

        $countryForm = $this->createCountryForm();
        $countryForm->submit($countryFormData);
        $this->assertTrue($countryForm->isValid(), 'Valid form');
    }

    public function testCodeIsUnique(): void
    {
        $countryFormData = $this->getFullCountryFormData();

        $countryForm = $this->createCountryForm();
        $countryForm->submit($countryFormData);
        $this->assertTrue($countryForm->isValid(), 'Non-existent country code');

        $countryFormData['code'] = 'UZ';
        $countryForm = $this->createCountryForm();
        $countryForm->submit($countryFormData);
        $this->assertFalse($countryForm->isValid(), 'Existing country code');
    }

    public function testCodeIsNotDuplicateOnEdit(): void
    {
        $countryFormData = $this->getFullCountryFormData();
        $countryFormData['code'] = 'UZ';

        $country = $this->countryFacade->findByCode('UZ');

        $countryForm = $this->createCountryForm($country);
        $countryForm->submit($countryFormData);
        $this->assertTrue($countryForm->isValid(), 'Existing country code on edit');
    }

    protected function setUp()
    {
        $translator = $this->createMock(Translator::class);
        $translator->method('staticTrans')->willReturnArgument(0);
        $translator->method('staticTransChoice')->willReturnArgument(0);
        Translator::injectSelf($translator);

        $this->localization = $this->createMock(Localization::class);
        $this->localization->method('getLocalesOfAllDomains')->willReturn(['cs', 'en']);
        $this->localization->method('getAdminLocale')->willReturn('en');

        $this->domain = $this->createMock(Domain::class);
        $this->domain->method('getAll')
            ->willReturn([
                    new DomainConfig(1, '', '', 'cs'),
                    new DomainConfig(2, '', '', 'en'),
                ]);
        $this->domain->method('getAllIds')->willReturn([1, 2]);

        $countryData = new CountryData();
        $countryData->code = 'UZ';
        $countryData->enabled = [1 => true, 2 => true];
        $countryData->names = ['cs' => 'Uzbekistán', 'en' => 'Uzbekistan'];

        $country = new Country($countryData);

        /* Entity returned by mock have to have Id properly set */
        $reflection = new ReflectionClass($country);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($country, 1);

        $this->countryFacade = $this->createMock(CountryFacade::class);
        $this->countryFacade->method('findByCode')->willReturnMap([['CZ', null], ['UZ', $country]]);

        parent::setUp();
    }

    /**
     * @return array
     */
    protected function getExtensions(): array
    {
        return [
            new ValidatorExtension(Validation::createValidator()),
            new PreloadedExtension(
                [
                new CountryFormType($this->countryFacade),
                new LocalizedType($this->localization),
                new DomainsType($this->domain),
                new MultidomainType($this->domain),
                ],
                []
            ),
        ];
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Country\Country|null $country
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createCountryForm(?Country $country = null): FormInterface
    {
        return $this->factory->create(CountryFormType::class, null, ['country' => $country]);
    }
}
