<?php

namespace Shopsys\ShopBundle\Form\Front\Customer;

use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Form\ValidationGroup;
use Shopsys\ShopBundle\Model\Customer\DeliveryAddressData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class DeliveryAddressFormType extends AbstractType
{

    const VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS = 'differentDeliveryAddress';

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
        return 'delivery_address_form';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('addressFilled', FormType::CHECKBOX, ['required' => false])
            ->add('companyName', FormType::TEXT, [
                'required' => false,
                'constraints' => [
                    new Constraints\Length([
                        'max' => 100,
                        'maxMessage' => 'Company name cannot be longer than {{ limit }} characters',
                        'groups' => [self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS],
                    ]),
                ],
            ])
            ->add('firstName', FormType::TEXT, [
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
            ->add('lastName', FormType::TEXT, [
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
            ->add('telephone', FormType::TEXT, [
                'required' => false,
                'constraints' => [
                    new Constraints\Length([
                        'max' => 30,
                        'maxMessage' => 'Telephone number cannot be longer than {{ limit }} characters',
                        'groups' => [self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS],
                    ]),
                ],
            ])
            ->add('street', FormType::TEXT, [
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
            ->add('city', FormType::TEXT, [
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
            ->add('postcode', FormType::TEXT, [
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
            ->add('country', FormType::CHOICE, [
                'required' => true,
                'choice_list' => new ObjectChoiceList($this->countries, 'name', [], null, 'id'),
            ]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults([
            'data_class' => DeliveryAddressData::class,
            'attr' => ['novalidate' => 'novalidate'],
            'validation_groups' => function (FormInterface $form) {
                $validationGroups = [ValidationGroup::VALIDATION_GROUP_DEFAULT];

                $deliveryAddressData = $form->getData();
                /* @var $customerData \Shopsys\ShopBundle\Model\Customer\DeliveryAddressData */

                if ($deliveryAddressData->addressFilled) {
                    $validationGroups[] = self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS;
                }

                return $validationGroups;
            },
        ]);
    }
}
