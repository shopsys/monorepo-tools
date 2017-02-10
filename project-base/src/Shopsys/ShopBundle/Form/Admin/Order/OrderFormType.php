<?php

namespace Shopsys\ShopBundle\Form\Admin\Order;

use Shopsys\ShopBundle\Component\Constraints\Email;
use Shopsys\ShopBundle\Form\Admin\Order\OrderItemFormType;
use Shopsys\ShopBundle\Form\Admin\Order\OrderTransportFormType;
use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Form\ValidationGroup;
use Shopsys\ShopBundle\Model\Country\Country;
use Shopsys\ShopBundle\Model\Order\OrderData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class OrderFormType extends AbstractType {

    const VALIDATION_GROUP_DELIVERY_ADDRESS_SAME_AS_BILLING_ADDRESS = 'deliveryAddressSameAsBillingAddress';

    /**
     * @var \Shopsys\ShopBundle\Model\Order\Status\OrderStatus[]
     */
    private $allOrderStatuses;

    /**
     * @var \Shopsys\ShopBundle\Model\Transport\Transport[]
     */
    private $transports;

    /**
     * @var \Shopsys\ShopBundle\Model\Payment\Payment[]
     */
    private $payments;

    /**
     * @var \Shopsys\ShopBundle\Model\Country\Country[]
     */
    private $countries;

    /**
     * @param array $allOrderStatuses
     * @param array $transports
     * @param array $payments
     * @param \Shopsys\ShopBundle\Model\Country\Country[] $countries
     */
    public function __construct(array $allOrderStatuses, array $transports, array $payments, array $countries) {
        $this->allOrderStatuses = $allOrderStatuses;
        $this->transports = $transports;
        $this->payments = $payments;
        $this->countries = $countries;
    }

    /**
     * @return string
     */
    public function getName() {
        return 'order_form';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('orderNumber', FormType::TEXT, ['read_only' => true])
            ->add('status', FormType::CHOICE, [
                'choice_list' => new ObjectChoiceList($this->allOrderStatuses, 'name', [], null, 'id'),
                'multiple' => false,
                'expanded' => false,
                'required' => true,
            ])
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
            ->add('companyName', FormType::TEXT, [
                'required' => false,
                'constraints' => [
                    new Constraints\Length(['max' => 100, 'maxMessage' => 'Company name cannot be longer than {{ limit }} characters']),
                ],
            ])
            ->add('companyNumber', FormType::TEXT, [
                'required' => false,
                'constraints' => [
                    new Constraints\Length(['max' => 50, 'maxMessage' => 'Identification number cannot be longer then {{ limit }} characters']),
                ],
            ])
            ->add('companyTaxNumber', FormType::TEXT, [
                'required' => false,
                'constraints' => [
                    new Constraints\Length(['max' => 50, 'maxMessage' => 'Tax number cannot be longer than {{ limit }} characters']),
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
                'choice_list' => new ObjectChoiceList($this->countries, 'name', [], null, 'id'),
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
            ->add('orderPayment', new OrderPaymentFormType($this->payments))
            ->add('orderTransport', new OrderTransportFormType($this->transports))
            ->add('save', FormType::SUBMIT);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults([
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
