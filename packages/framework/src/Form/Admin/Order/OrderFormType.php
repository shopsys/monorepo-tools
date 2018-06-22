<?php

namespace Shopsys\FrameworkBundle\Form\Admin\Order;

use Shopsys\FrameworkBundle\Form\Constraints\Email;
use Shopsys\FrameworkBundle\Form\DisplayOnlyType;
use Shopsys\FrameworkBundle\Form\ValidationGroup;
use Shopsys\FrameworkBundle\Model\Country\CountryFacade;
use Shopsys\FrameworkBundle\Model\Order\Order;
use Shopsys\FrameworkBundle\Model\Order\OrderData;
use Shopsys\FrameworkBundle\Model\Order\Status\OrderStatusFacade;
use Shopsys\FrameworkBundle\Model\Payment\PaymentFacade;
use Shopsys\FrameworkBundle\Model\Transport\TransportFacade;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class OrderFormType extends AbstractType
{
    const VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS = 'deliveryAddressSameAsBillingAddress';

    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade
     */
    private $paymentFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\TransportFacade
     */
    private $transportFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Country\CountryFacade
     */
    private $countryFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatusFacade
     */
    private $orderStatusFacade;

    public function __construct(
        PaymentFacade $paymentFacade,
        TransportFacade $transportFacade,
        CountryFacade $countryFacade,
        OrderStatusFacade $orderStatusFacade
    ) {
        $this->paymentFacade = $paymentFacade;
        $this->transportFacade = $transportFacade;
        $this->countryFacade = $countryFacade;
        $this->orderStatusFacade = $orderStatusFacade;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $domainId = $options['order']->getDomainId();
        $countries = $this->countryFacade->getAllByDomainId($domainId);

        $payments = $this->paymentFacade->getVisibleByDomainId($domainId);
        if (!in_array($options['order']->getPayment(), $payments, true)) {
            $payments[] = $options['order']->getPayment();
        }

        $transports = $this->transportFacade->getVisibleByDomainId($domainId, $payments);
        if (!in_array($options['order']->getTransport(), $transports, true)) {
            $transports[] = $options['order']->getTransport();
        }

        $builder
            ->add('orderNumber', DisplayOnlyType::class)
            ->add('status', ChoiceType::class, [
                'required' => true,
                'choices' => $this->orderStatusFacade->getAll(),
                'choice_label' => 'name',
                'choice_value' => 'id',
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('firstName', TextType::class, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter first name']),
                    new Constraints\Length([
                        'max' => 100,
                        'maxMessage' => 'First name cannot be longer then {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('lastName', TextType::class, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter last name']),
                    new Constraints\Length([
                        'max' => 100,
                        'maxMessage' => 'Last name cannot be longer than {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter e-mail']),
                    new Email(['message' => 'Please enter valid e-mail']),
                    new Constraints\Length([
                        'max' => 255,
                        'maxMessage' => 'Email cannot be longer then {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('telephone', TextType::class, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter telephone number']),
                    new Constraints\Length([
                        'max' => 30,
                        'maxMessage' => 'Telephone number cannot be longer than {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('companyName', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Constraints\Length([
                        'max' => 100,
                        'maxMessage' => 'Company name cannot be longer than {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('companyNumber', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Constraints\Length([
                        'max' => 50,
                        'maxMessage' => 'Identification number cannot be longer then {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('companyTaxNumber', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Constraints\Length([
                        'max' => 50,
                        'maxMessage' => 'Tax number cannot be longer than {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('street', TextType::class, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter street']),
                    new Constraints\Length([
                        'max' => 100,
                        'maxMessage' => 'Street name cannot be longer than {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('city', TextType::class, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter city']),
                    new Constraints\Length([
                        'max' => 100,
                        'maxMessage' => 'City name cannot be longer than {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('postcode', TextType::class, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter zip code']),
                    new Constraints\Length([
                        'max' => 30,
                        'maxMessage' => 'Zip code cannot be longer than {{ limit }} characters',
                    ]),
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
            ->add('deliveryAddressSameAsBillingAddress', CheckboxType::class, ['required' => false])
            ->add('deliveryFirstName', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please enter first name of contact person',
                        'groups' => [self::VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS],
                    ]),
                    new Constraints\Length([
                        'max' => 100,
                        'maxMessage' => 'First name of contact person cannot be longer then {{ limit }} characters',
                        'groups' => [self::VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS],
                    ]),
                ],
            ])
            ->add('deliveryLastName', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please enter last name of contact person',
                        'groups' => [self::VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS],
                    ]),
                    new Constraints\Length([
                        'max' => 100,
                        'maxMessage' => 'Last name of contact person cannot be longer than {{ limit }} characters',
                        'groups' => [self::VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS],
                    ]),
                ],
            ])
            ->add('deliveryCompanyName', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Constraints\Length([
                        'max' => 100,
                        'maxMessage' => 'Name cannot be longer than {{ limit }} characters',
                        'groups' => [self::VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS],
                    ]),
                ],
            ])
            ->add('deliveryTelephone', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Constraints\Length([
                        'max' => 30,
                        'maxMessage' => 'Telephone number cannot be longer than {{ limit }} characters',
                        'groups' => [self::VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS],
                    ]),
                ],
            ])
            ->add('deliveryStreet', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please enter street',
                        'groups' => [self::VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS],
                    ]),
                    new Constraints\Length([
                        'max' => 100,
                        'maxMessage' => 'Street name cannot be longer than {{ limit }} characters',
                        'groups' => [self::VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS],
                    ]),
                ],
            ])
            ->add('deliveryCity', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please enter city',
                        'groups' => [self::VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS],
                    ]),
                    new Constraints\Length(['max' => 100,
                        'maxMessage' => 'City name cannot be longer than {{ limit }} characters',
                        'groups' => [self::VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS],
                    ]),
                ],
            ])
            ->add('deliveryPostcode', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please enter zip code',
                        'groups' => [self::VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS],
                    ]),
                    new Constraints\Length([
                        'max' => 30,
                        'maxMessage' => 'Zip code cannot be longer than {{ limit }} characters',
                        'groups' => [self::VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS],
                    ]),
                ],
            ])
            ->add('deliveryCountry', ChoiceType::class, [
                'required' => true,
                'choices' => $countries,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please choose country']),
                ],
            ])
            ->add('note', TextareaType::class, ['required' => false])
            ->add('itemsWithoutTransportAndPayment', CollectionType::class, [
                'entry_type' => OrderItemFormType::class,
                'error_bubbling' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('orderPayment', OrderPaymentFormType::class, [
                'payments' => $payments,
            ])
            ->add('orderTransport', OrderTransportFormType::class, [
                'transports' => $transports,
            ])
            ->add('save', SubmitType::class);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('order')
            ->setAllowedTypes('order', Order::class)
            ->setDefaults([
                'data_class' => OrderData::class,
                'attr' => ['novalidate' => 'novalidate'],
                'validation_groups' => function (FormInterface $form) {
                    $validationGroups = [ValidationGroup::VALIDATION_GROUP_DEFAULT];

                    $orderData = $form->getData();
                    /* @var $data \Shopsys\FrameworkBundle\Model\Order\OrderData */

                    if (!$orderData->deliveryAddressSameAsBillingAddress) {
                        $validationGroups[] = self::VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS;
                    }

                    return $validationGroups;
                },
            ]);
    }
}
