<?php

namespace Shopsys\ShopBundle\Form\Admin\Order;

use Shopsys\ShopBundle\Component\Constraints\Email;
use Shopsys\ShopBundle\Form\Admin\Order\OrderItemFormType;
use Shopsys\ShopBundle\Form\Admin\Order\OrderTransportFormType;
use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Form\ValidationGroup;
use Shopsys\ShopBundle\Model\Country\Country;
use Shopsys\ShopBundle\Model\Country\CountryFacade;
use Shopsys\ShopBundle\Model\Order\Order;
use Shopsys\ShopBundle\Model\Order\OrderData;
use Shopsys\ShopBundle\Model\Order\Status\OrderStatusFacade;
use Shopsys\ShopBundle\Model\Payment\PaymentFacade;
use Shopsys\ShopBundle\Model\Transport\TransportFacade;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class OrderFormType extends AbstractType
{
    const VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS = 'deliveryAddressSameAsBillingAddress';

    /**
     * @var \Shopsys\ShopBundle\Model\Payment\PaymentFacade
     */
    private $paymentFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Transport\TransportFacade
     */
    private $transportFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Country\CountryFacade
     */
    private $countryFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\Status\OrderStatusFacade
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
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
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
            ->add('orderNumber', FormType::TEXT, ['read_only' => true])
            ->add('status', FormType::CHOICE, [
                'choice_list' => new ObjectChoiceList($this->orderStatusFacade->getAll(), 'name', [], null, 'id'),
                'multiple' => false,
                'expanded' => false,
                'required' => true,
            ])
            ->add('firstName', FormType::TEXT, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter first name']),
                    new Constraints\Length([
                        'max' => 100,
                        'maxMessage' => 'First name cannot be longer then {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('lastName', FormType::TEXT, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter surname']),
                    new Constraints\Length([
                        'max' => 100,
                        'maxMessage' => 'Surname cannot be longer than {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('email', FormType::EMAIL, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter e-mail']),
                    new Email(['message' => 'Please enter valid e-mail']),
                    new Constraints\Length([
                        'max' => 255,
                        'maxMessage' => 'Email cannot be longer then {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('telephone', FormType::TEXT, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter telephone number']),
                    new Constraints\Length([
                        'max' => 30,
                        'maxMessage' => 'Telephone number cannot be longer than {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('companyName', FormType::TEXT, [
                'required' => false,
                'constraints' => [
                    new Constraints\Length([
                        'max' => 100,
                        'maxMessage' => 'Company name cannot be longer than {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('companyNumber', FormType::TEXT, [
                'required' => false,
                'constraints' => [
                    new Constraints\Length([
                        'max' => 50,
                        'maxMessage' => 'Identification number cannot be longer then {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('companyTaxNumber', FormType::TEXT, [
                'required' => false,
                'constraints' => [
                    new Constraints\Length([
                        'max' => 50,
                        'maxMessage' => 'Tax number cannot be longer than {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('street', FormType::TEXT, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter street']),
                    new Constraints\Length([
                        'max' => 100,
                        'maxMessage' => 'Street name cannot be longer than {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('city', FormType::TEXT, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter city']),
                    new Constraints\Length([
                        'max' => 100,
                        'maxMessage' => 'City name cannot be longer than {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('postcode', FormType::TEXT, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter zip code']),
                    new Constraints\Length([
                        'max' => 30,
                        'maxMessage' => 'Zip code cannot be longer than {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('country', FormType::CHOICE, [
                'choice_list' => new ObjectChoiceList($countries, 'name', [], null, 'id'),
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please choose country']),
                ],
            ])
            ->add('deliveryAddressSameAsBillingAddress', FormType::CHECKBOX, ['required' => false])
            ->add('deliveryFirstName', FormType::TEXT, [
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
            ->add('deliveryLastName', FormType::TEXT, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please enter surname of contact person',
                        'groups' => [self::VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS],
                    ]),
                    new Constraints\Length([
                        'max' => 100,
                        'maxMessage' => 'Surname of contact person cannot be longer than {{ limit }} characters',
                        'groups' => [self::VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS],
                    ]),
                ],
            ])
            ->add('deliveryCompanyName', FormType::TEXT, [
                'required' => false,
                'constraints' => [
                    new Constraints\Length([
                        'max' => 100,
                        'maxMessage' => 'Name cannot be longer than {{ limit }} characters',
                        'groups' => [self::VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS],
                    ]),
                ],
            ])
            ->add('deliveryTelephone', FormType::TEXT, [
                'required' => false,
                'constraints' => [
                    new Constraints\Length([
                        'max' => 30,
                        'maxMessage' => 'Telephone number cannot be longer than {{ limit }} characters',
                        'groups' => [self::VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS],
                    ]),
                ],
            ])
            ->add('deliveryStreet', FormType::TEXT, [
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
            ->add('deliveryCity', FormType::TEXT, [
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
            ->add('deliveryPostcode', FormType::TEXT, [
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
            ->add('deliveryCountry', FormType::CHOICE, [
                'required' => true,
                'choice_list' => new ObjectChoiceList($countries, 'name', [], null, 'id'),
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please choose country']),
                ],
            ])
            ->add('note', FormType::TEXTAREA, ['required' => false])
            ->add('itemsWithoutTransportAndPayment', FormType::COLLECTION, [
                'type' => new OrderItemFormType(),
                'error_bubbling' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('orderPayment', new OrderPaymentFormType($payments))
            ->add('orderTransport', new OrderTransportFormType($transports))
            ->add('save', FormType::SUBMIT);
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
                    /* @var $data \Shopsys\ShopBundle\Model\Order\OrderData */

                    if (!$orderData->deliveryAddressSameAsBillingAddress) {
                        $validationGroups[] = self::VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS;
                    }

                    return $validationGroups;
                },
            ]);
    }
}
