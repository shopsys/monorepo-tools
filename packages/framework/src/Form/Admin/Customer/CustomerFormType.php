<?php

namespace Shopsys\FrameworkBundle\Form\Admin\Customer;

use Shopsys\FrameworkBundle\Form\OrderListType;
use Shopsys\FrameworkBundle\Model\Customer\CustomerDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Customer\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerFormType extends AbstractType
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\CustomerDataFactoryInterface
     */
    private $customerDataFactory;

    public function __construct(CustomerDataFactoryInterface $customerDataFactory)
    {
        $this->customerDataFactory = $customerDataFactory;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userData', UserFormType::class, [
                'user' => $options['user'],
                'domain_id' => $options['domain_id'],
                'render_form_row' => false,
            ])
            ->add('billingAddressData', BillingAddressFormType::class, [
                'domain_id' => $options['domain_id'],
                'render_form_row' => false,
            ])
            ->add('deliveryAddressData', DeliveryAddressFormType::class, [
                'domain_id' => $options['domain_id'],
                'render_form_row' => false,
            ])
            ->add('save', SubmitType::class);

        if ($options['user'] === null) {
            $builder->add('sendRegistrationMail', CheckboxType::class, [
                'required' => false,
                'label' => t('Send confirmation e-mail about registration to customer'),
            ]);
        } else {
            $builder->add('orders', OrderListType::class, [
                'user' => $options['user'],
            ]);
        }
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['user', 'domain_id'])
            ->setAllowedTypes('user', [User::class, 'null'])
            ->setAllowedTypes('domain_id', 'int')
            ->setDefaults([
                'empty_data' => $this->customerDataFactory->create(),
                'attr' => ['novalidate' => 'novalidate'],
            ]);
    }
}
