<?php

namespace Shopsys\ShopBundle\Form\Front\Cart;

use Shopsys\ShopBundle\Form\FormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class AddProductFormType extends AbstractType
{

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('productId', FormType::HIDDEN, [
                    'constraints' => [
                        new Constraints\GreaterThan(0),
                        new Constraints\Regex(['pattern' => '/^\d+$/']),
                    ],
                ]
            )
            ->add('quantity', FormType::TEXT, [
                    'data' => 1,
                    'constraints' => [
                        new Constraints\GreaterThan(0),
                        new Constraints\Regex(['pattern' => '/^\d+$/']),
                    ],
                ]
            )
            ->add('add', FormType::SUBMIT);
    }

    /**
     * @return string
     */
    public function getName() {
        return 'add_product_form';
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults([
            'attr' => ['novalidate' => 'novalidate'],
            'csrf_protection' => false, // CSRF is not necessary (and can be annoying) in this form
        ]);
    }
}
