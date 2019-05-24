<?php

namespace Shopsys\FrameworkBundle\Form\Admin\Country;

use Shopsys\FormTypesBundle\MultidomainType;
use Shopsys\FrameworkBundle\Form\Constraints\NotInArray;
use Shopsys\FrameworkBundle\Form\DisplayOnlyType;
use Shopsys\FrameworkBundle\Form\DomainsType;
use Shopsys\FrameworkBundle\Form\Locale\LocalizedType;
use Shopsys\FrameworkBundle\Model\Country\Country;
use Shopsys\FrameworkBundle\Model\Country\CountryData;
use Shopsys\FrameworkBundle\Model\Country\CountryFacade;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CountryFormType extends AbstractType
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Country\Country|null
     */
    protected $country;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Country\CountryFacade
     */
    protected $countryFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Country\CountryFacade $countryFacade
     */
    public function __construct(CountryFacade $countryFacade)
    {
        $this->countryFacade = $countryFacade;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var \Shopsys\FrameworkBundle\Model\Country\Country|null $country */
        $this->country = $options['country'];

        if ($this->country instanceof Country) {
            $builder->add('formId', DisplayOnlyType::class, [
                'label' => t('ID'),
                'data' => $this->country->getId(),
            ]);
        }

        $builder
            ->add('names', LocalizedType::class, [
                'required' => true,
                'main_constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter country name']),
                ],
                'entry_options' => [
                    'required' => true,
                    'constraints' => [
                        new Constraints\NotBlank(['message' => 'Please enter country name']),
                        new Constraints\Length(['max' => 255, 'maxMessage' => 'Country name cannot be longer than {{ limit }} characters']),
                    ],
                ],
                'label' => t('Name'),
            ])
            ->add('code', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter country code']),
                    new Constraints\Length(['max' => 2, 'maxMessage' => 'Country code cannot be longer than {{ limit }} characters']),
                    new NotInArray([
                        'array' => $this->getOtherCountryCodes(),
                        'message' => 'Country code with this code already exists',
                    ]),
                ],
                'label' => t('Code'),
                'attr' => [
                    'icon' => true,
                    'iconTitle' => t('Country code in ISO 3166-1 alpha-2'),
                ],
            ])
            ->add('enabled', DomainsType::class, [
                'required' => false,
                'label' => t('Display on'),
            ])
            ->add('priority', MultidomainType::class, [
                'entry_type' => TextType::class,
                'entry_options' => [
                    'attr' => [
                        'icon' => true,
                        'iconTitle' => t('The higher the priority, the higher the country will be shown in the listings. Countries with the same priority will be sorted alphabetically.'),
                    ],
                    'required' => false,
                    'constraints' => [
                        new Constraints\Type(['type' => 'numeric']),
                        new Constraints\GreaterThanOrEqual(['value' => 0]),
                    ],
                ],
                'required' => false,
                'label' => t('Priority'),
            ])
            ->add('save', SubmitType::class);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefined('country')
            ->setAllowedTypes('country', [Country::class, 'null'])
            ->setDefaults([
                'data_class' => CountryData::class,
                'attr' => ['novalidate' => 'novalidate'],
            ]);
    }

    /**
     * @param mixed $countryCodeValue
     * @param \Symfony\Component\Validator\Context\ExecutionContextInterface $context
     * @deprecated Validation using this method as callback is deprecated since SSFW 7.3, use NotInArray instead
     */
    public function validateUniqueCode($countryCodeValue, ExecutionContextInterface $context): void
    {
        @trigger_error(
            sprintf('Validation using method "%s" as callback is deprecated since SSFW 7.3, use %s instead', __METHOD__, NotInArray::class),
            E_USER_DEPRECATED
        );

        if (in_array($countryCodeValue, $this->getOtherCountryCodes(), true)) {
            $context->addViolation('Country code with this code already exists');
        }
    }

    /**
     * @return string[]
     */
    protected function getOtherCountryCodes(): array
    {
        $otherCountryCodes = [];
        foreach ($this->countryFacade->getAll() as $country) {
            if ($country !== $this->country) {
                $otherCountryCodes[] = $country->getCode();
            }
        }

        return $otherCountryCodes;
    }
}
