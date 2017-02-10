<?php

namespace Shopsys\ShopBundle\Form\Front\Order;

use Shopsys\ShopBundle\Component\Constraints\Email;
use Shopsys\ShopBundle\Component\Transformers\InverseTransformer;
use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Form\ValidationGroup;
use Shopsys\ShopBundle\Model\Country\Country;
use Shopsys\ShopBundle\Model\Order\FrontOrderData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class PersonalInfoFormType extends AbstractType
{
    const VALIDATION_GROUP_COMPANY_CUSTOMER = 'companyCustomer';
    const VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS = 'differentDeliveryAddress';

    /**
     * @var \Shopsys\ShopBundle\Model\Country\Country[]
     */
    private $countries;

    /**
     * @param \Shopsys\ShopBundle\Model\Country\Country[] $countries
     */
    public function __construct(array $countries)
    {
        $this->countries = $countries;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', FormType::TEXT, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter first name']),
                    new Constraints\Length(['max' => 100, 'maxMessage' => 'First name cannot be longer then {{ limit }} characters']),
                ],
            ])
            ->add('lastName', FormType::TEXT, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter surname']),
                    new Constraints\Length(['max' => 100, 'maxMessage' => 'Surname cannot be longer than {{ limit }} characters']),
                ],
            ])
            ->add('email', FormType::EMAIL, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter e-mail']),
                    new Email(['message' => 'Please enter valid e-mail']),
                    new Constraints\Length(['max' => 255, 'maxMessage' => 'Email cannot be longer then {{ limit }} characters']),
                ],
            ])
            ->add('telephone', FormType::TEXT, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter telephone number']),
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
                    new Constraints\Length(['max' => 100,
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
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter street']),
                    new Constraints\Length(['max' => 100, 'maxMessage' => 'Street name cannot be longer than {{ limit }} characters']),
                ],
            ])
            ->add('city', FormType::TEXT, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter city']),
                    new Constraints\Length(['max' => 100, 'maxMessage' => 'City name cannot be longer than {{ limit }} characters']),
                ],
            ])
            ->add('postcode', FormType::TEXT, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter zip code']),
                    new Constraints\Length(['max' => 30, 'maxMessage' => 'Zip code cannot be longer than {{ limit }} characters']),
                ],
            ])
            ->add('country', FormType::CHOICE, [
                'choice_list' => new ObjectChoiceList($this->countries, 'name', [], null, 'id'),
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please choose country']),
                ],
            ])
            ->add($builder
                ->create('deliveryAddressFilled', FormType::CHECKBOX, [
                    'required' => false,
                    'property_path' => 'deliveryAddressSameAsBillingAddress',
                ])
                ->addModelTransformer(new InverseTransformer())
            )
            ->add('deliveryFirstName', FormType::TEXT, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please enter first name of contact person',
                        'groups' => [self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS],
                    ]),
                    new Constraints\Length([
                        'max' => 100,
                        'maxMessage' => 'First name of contact person cannot be longer then {{ limit }} characters',
                        'groups' => [self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS],
                    ]),
                ],
            ])
            ->add('deliveryLastName', FormType::TEXT, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please enter surname of contact person',
                        'groups' => [self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS],
                    ]),
                    new Constraints\Length([
                        'max' => 100,
                        'maxMessage' => 'Surname of contact person cannot be longer than {{ limit }} characters',
                        'groups' => [self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS],
                    ]),
                ],
            ])
            ->add('deliveryCompanyName', FormType::TEXT, [
                'required' => false,
                'constraints' => [
                    new Constraints\Length([
                        'max' => 100,
                        'maxMessage' => 'Company name cannot be longer than {{ limit }} characters',
                        'groups' => [self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS],
                    ]),
                ],
            ])
            ->add('deliveryTelephone', FormType::TEXT, [
                'required' => false,
                'constraints' => [
                    new Constraints\Length([
                        'max' => 30,
                        'maxMessage' => 'Telephone number cannot be longer than {{ limit }} characters',
                        'groups' => [self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS],
                    ]),
                ],
            ])
            ->add('deliveryStreet', FormType::TEXT, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please enter street',
                        'groups' => [self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS],
                    ]),
                    new Constraints\Length([
                        'max' => 100,
                        'maxMessage' => 'Street name cannot be longer than {{ limit }} characters',
                        'groups' => [self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS],
                    ]),
                ],
            ])
            ->add('deliveryCity', FormType::TEXT, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please enter city',
                        'groups' => [self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS],
                    ]),
                    new Constraints\Length([
                        'max' => 100,
                        'maxMessage' => 'City name cannot be longer than {{ limit }} characters',
                        'groups' => [self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS],
                    ]),
                ],
            ])
            ->add('deliveryPostcode', FormType::TEXT, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please enter zip code',
                        'groups' => [self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS],
                    ]),
                    new Constraints\Length([
                        'max' => 30,
                        'maxMessage' => 'Zip code cannot be longer than {{ limit }} characters',
                        'groups' => [self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS],
                    ]),
                ],
            ])
            ->add('deliveryCountry', FormType::CHOICE, [
                'required' => true,
                'choice_list' => new ObjectChoiceList($this->countries, 'name', [], null, 'id'),
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please choose country',
                        'groups' => [self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS],
                    ]),
                ],
            ])
            ->add('note', FormType::TEXTAREA, ['required' => false])
            ->add('termsAndConditionsAgreement', FormType::CHECKBOX, [
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'You have to agree with terms and conditions',
                    ]),
                ],
            ])
            ->add('newsletterSubscription', FormType::CHECKBOX, [
                'required' => false,
            ])
            ->add('save', FormType::SUBMIT);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'order_personal_info_form';
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FrontOrderData::class,
            'attr' => ['novalidate' => 'novalidate'],
            'validation_groups' => function (FormInterface $form) {
                $validationGroups = [ValidationGroup::VALIDATION_GROUP_DEFAULT];

                $orderData = $form->getData();
                /* @var $data \Shopsys\ShopBundle\Model\Order\OrderData */

                if ($orderData->companyCustomer) {
                    $validationGroups[] = self::VALIDATION_GROUP_COMPANY_CUSTOMER;
                }
                if (!$orderData->deliveryAddressSameAsBillingAddress) {
                    $validationGroups[] = self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS;
                }

                return $validationGroups;
            },
        ]);
    }
}
