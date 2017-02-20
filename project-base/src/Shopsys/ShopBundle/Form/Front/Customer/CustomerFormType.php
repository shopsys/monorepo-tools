<?php

namespace Shopsys\ShopBundle\Form\Front\Customer;

use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Form\Front\Customer\UserFormType;
use Shopsys\ShopBundle\Model\Customer\CustomerData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerFormType extends AbstractType
{
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
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userData', new UserFormType())
            ->add('billingAddressData', new BillingAddressFormType($this->countries))
            ->add('deliveryAddressData', new DeliveryAddressFormType($this->countries))
            ->add('save', FormType::SUBMIT);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CustomerData::class,
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
