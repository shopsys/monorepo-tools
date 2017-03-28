<?php

namespace Shopsys\ShopBundle\Form\Front\Order;

use Shopsys\ShopBundle\Component\Constraints\Email;
use Shopsys\ShopBundle\Component\Transformers\InverseTransformer;
use Shopsys\ShopBundle\Form\ValidationGroup;
use Shopsys\ShopBundle\Model\Country\CountryFacade;
use Shopsys\ShopBundle\Model\Order\FrontOrderData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class PersonalInfoFormType extends AbstractType
{
    const VALIDATION_GROUP_COMPANY_CUSTOMER = 'companyCustomer';
    const VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS = 'differentDeliveryAddress';

    /**
     * @var \Shopsys\ShopBundle\Model\Country\CountryFacade
     */
    private $countryFacade;

    /**
     * @param \Shopsys\ShopBundle\Model\Country\CountryFacade $countryFacade
     */
    public function __construct(CountryFacade $countryFacade)
    {
        $this->countryFacade = $countryFacade;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $countries = $this->countryFacade->getAllByDomainId($options['domain_id']);

        $builder
            ->add('firstName', TextType::class, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter first name']),
                    new Constraints\Length(['max' => 100, 'maxMessage' => 'First name cannot be longer then {{ limit }} characters']),
                ],
            ])
            ->add('lastName', TextType::class, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter surname']),
                    new Constraints\Length(['max' => 100, 'maxMessage' => 'Surname cannot be longer than {{ limit }} characters']),
                ],
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter e-mail']),
                    new Email(['message' => 'Please enter valid e-mail']),
                    new Constraints\Length(['max' => 255, 'maxMessage' => 'Email cannot be longer then {{ limit }} characters']),
                ],
            ])
            ->add('telephone', TextType::class, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter telephone number']),
                    new Constraints\Length(['max' => 30, 'maxMessage' => 'Telephone number cannot be longer than {{ limit }} characters']),
                ],
            ])
            ->add('companyCustomer', CheckboxType::class, ['required' => false])
            ->add('companyName', TextType::class, [
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
            ->add('companyNumber', TextType::class, [
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
            ->add('companyTaxNumber', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Constraints\Length([
                        'max' => 50,
                        'maxMessage' => 'Tax number cannot be longer than {{ limit }} characters',
                        'groups' => [self::VALIDATION_GROUP_COMPANY_CUSTOMER],
                    ]),
                ],
            ])
            ->add('street', TextType::class, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter street']),
                    new Constraints\Length(['max' => 100, 'maxMessage' => 'Street name cannot be longer than {{ limit }} characters']),
                ],
            ])
            ->add('city', TextType::class, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter city']),
                    new Constraints\Length(['max' => 100, 'maxMessage' => 'City name cannot be longer than {{ limit }} characters']),
                ],
            ])
            ->add('postcode', TextType::class, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter zip code']),
                    new Constraints\Length(['max' => 30, 'maxMessage' => 'Zip code cannot be longer than {{ limit }} characters']),
                ],
            ])
            ->add('country', ChoiceType::class, [
                'choices' => $countries,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please choose country']),
                ],
            ])
            ->add($builder
                ->create('deliveryAddressFilled', CheckboxType::class, [
                    'required' => false,
                    'property_path' => 'deliveryAddressSameAsBillingAddress',
                ])
                ->addModelTransformer(new InverseTransformer()))
            ->add('deliveryFirstName', TextType::class, [
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
            ->add('deliveryLastName', TextType::class, [
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
            ->add('deliveryCompanyName', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Constraints\Length([
                        'max' => 100,
                        'maxMessage' => 'Company name cannot be longer than {{ limit }} characters',
                        'groups' => [self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS],
                    ]),
                ],
            ])
            ->add('deliveryTelephone', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Constraints\Length([
                        'max' => 30,
                        'maxMessage' => 'Telephone number cannot be longer than {{ limit }} characters',
                        'groups' => [self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS],
                    ]),
                ],
            ])
            ->add('deliveryStreet', TextType::class, [
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
            ->add('deliveryCity', TextType::class, [
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
            ->add('deliveryPostcode', TextType::class, [
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
            ->add('deliveryCountry', ChoiceType::class, [
                'required' => true,
                'choices' => $countries,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please choose country',
                        'groups' => [self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS],
                    ]),
                ],
            ])
            ->add('note', TextareaType::class, ['required' => false])
            ->add('termsAndConditionsAgreement', CheckboxType::class, [
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'You have to agree with terms and conditions',
                    ]),
                ],
            ])
            ->add('newsletterSubscription', CheckboxType::class, [
                'required' => false,
            ])
            ->add('save', SubmitType::class);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'order_personal_info_form';
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('domain_id')
            ->setAllowedTypes('domain_id', 'int')
            ->setDefaults([
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
