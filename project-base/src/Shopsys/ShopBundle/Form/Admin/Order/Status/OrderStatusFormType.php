<?php

namespace Shopsys\ShopBundle\Form\Admin\Order\Status;

use Shopsys\ShopBundle\Form\Locale\LocalizedType;
use Shopsys\ShopBundle\Model\Order\Status\OrderStatusData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class OrderStatusFormType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', LocalizedType::class, [
                'options' => [
                    'constraints' => [
                        new Constraints\NotBlank(['message' => 'Please enter all country names']),
                        new Constraints\Length(['max' => 255, 'maxMessage' => 'Status name cannot be longer than {{ limit }} characters']),
                    ],
                ],
            ]);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OrderStatusData::class,
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
