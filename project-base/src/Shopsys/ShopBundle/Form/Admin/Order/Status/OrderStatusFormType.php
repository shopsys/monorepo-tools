<?php

namespace Shopsys\ShopBundle\Form\Admin\Order\Status;

use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Model\Order\Status\OrderStatusData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class OrderStatusFormType extends AbstractType {

    /**
     * @return string
     */
    public function getName() {
        return 'order_status_form';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', FormType::LOCALIZED, [
                'options' => [
                    'constraints' => [
                        new Constraints\NotBlank(['message' => 'Please enter all country names']),
                        new Constraints\Length(['max' => 255, 'maxMessage' => 'Status name cannot be longer than {{ limit }} characters']),
                    ],
                ],
            ]);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults([
            'data_class' => OrderStatusData::class,
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }

}
