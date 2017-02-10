<?php

namespace Shopsys\ShopBundle\Form\Admin\BestsellingProduct;

use Shopsys\ShopBundle\Component\Constraints;
use Shopsys\ShopBundle\Form\FormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BestsellingProductFormType extends AbstractType
{
    /**
     * @return string
     */
    public function getName() {
        return 'bestselling_product_form';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('products', FormType::FORM, [
                'error_bubbling' => true,
                'constraints' => [
                    new Constraints\UniqueCollection([
                        'allowEmpty' => true,
                        'message' => 'You entered same product twice. In list of bestsellers can be product only once. '
                            . 'Please correct it and then save form again.',
                    ]),
                ],
            ])
            ->add('save', FormType::SUBMIT);

        for ($i = 0; $i < 10; $i++) {
            $builder->get('products')
                ->add($i, FormType::PRODUCT, [
                    'required' => false,
                    'placeholder' => null,
                    'enableRemove' => true,
                ]);
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults([
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
