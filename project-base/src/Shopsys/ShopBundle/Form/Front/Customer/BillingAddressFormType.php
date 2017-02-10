<?php

namespace Shopsys\ShopBundle\Form\Front\Customer;

use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Form\ValidationGroup;
use Shopsys\ShopBundle\Model\Customer\BillingAddressData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class BillingAddressFormType extends AbstractType
{

    const VALIDATION_GROUP_COMPANY_CUSTOMER = 'companyCustomer';

    /**
     * @var \Shopsys\ShopBundle\Model\Country\Country[]
     */
    private $countries;

    /**
     * @param \Shopsys\ShopBundle\Model\Country\Country[] $countries
     */
    public function __construct(array $countries) {
        $this->countries = $countries;
    }

    /**
     * @return string
     */
    public function getName() {
        return 'billing_address_form';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('telephone', FormType::TEXT, [
                'required' => false,
                'constraints' => [
                    new Constraints\Length(['max' => 30, 'maxMessage' => 'Telephone number cannot be longer than {{ limit }} characters']),
                ],
            ])
            ->add('companyCustomer', FormType::CHECKBOX, ['required' => false])
            ->add('companyName', FormType::TEXT, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please enter company name',
                        'groups' => [self::VALIDATION_GROUP_COMPANY_CUSTOMER],
                    ]),
                    new Constraints\Length([
                        'max' => 100,
                        'maxMessage' => 'Company name cannot be longer than {{ limit }} characters',
                        'groups' => [self::VALIDATION_GROUP_COMPANY_CUSTOMER],
                    ]),
                ],
            ])
            ->add('companyNumber', FormType::TEXT, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please enter identification number',
                        'groups' => [self::VALIDATION_GROUP_COMPANY_CUSTOMER],
                    ]),
                    new Constraints\Length([
                        'max' => 50,
                        'maxMessage' => 'Identification number cannot be longer then {{ limit }} characters',
                        'groups' => [self::VALIDATION_GROUP_COMPANY_CUSTOMER],
                    ]),
                ],
            ])
            ->add('companyTaxNumber', FormType::TEXT, [
                'required' => false,
                'constraints' => [
                    new Constraints\Length([
                        'max' => 50,
                        'maxMessage' => 'Tax number cannot be longer than {{ limit }} characters',
                        'groups' => [self::VALIDATION_GROUP_COMPANY_CUSTOMER],
                    ]),
                ],
            ])
            ->add('street', FormType::TEXT, [
                'required' => false,
                'constraints' => [
                    new Constraints\Length(['max' => 100, 'maxMessage' => 'Street name cannot be longer than {{ limit }} characters']),
                ],
            ])
            ->add('city', FormType::TEXT, [
                'required' => false,
                'constraints' => [
                    new Constraints\Length(['max' => 100, 'maxMessage' => 'City name cannot be longer than {{ limit }} characters']),
                ],
            ])
            ->add('postcode', FormType::TEXT, [
                'required' => false,
                'constraints' => [
                    new Constraints\Length(['max' => 30, 'maxMessage' => 'Zip code cannot be longer than {{ limit }} characters']),
                ],
            ])
            ->add('country', FormType::CHOICE, [
                'required' => false,
                'choice_list' => new ObjectChoiceList($this->countries, 'name', [], null, 'id'),
            ]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults([
            'data_class' => BillingAddressData::class,
            'attr' => ['novalidate' => 'novalidate'],
            'validation_groups' => function (FormInterface $form) {
                $validationGroups = [ValidationGroup::VALIDATION_GROUP_DEFAULT];

                $billingAddressData = $form->getData();
                /* @var $billingAddressData \Shopsys\ShopBundle\Model\Customer\BillingAddressData */

                if ($billingAddressData->companyCustomer) {
                    $validationGroups[] = self::VALIDATION_GROUP_COMPANY_CUSTOMER;
                }

                return $validationGroups;
            },
        ]);
    }

}
