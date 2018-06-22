<?php

namespace Shopsys\FrameworkBundle\Form\Admin\BestsellingProduct;

use Shopsys\FrameworkBundle\Form\Constraints;
use Shopsys\FrameworkBundle\Form\ProductType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BestsellingProductFormType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('products', FormType::class, [
                'error_bubbling' => true,
                'constraints' => [
                    new Constraints\UniqueCollection([
                        'allowEmpty' => true,
                        'message' => 'You entered same product twice. In list of bestsellers can be product only once. '
                            . 'Please correct it and then save form again.',
                    ]),
                ],
            ])
            ->add('save', SubmitType::class);

        for ($i = 0; $i < 10; $i++) {
            $builder->get('products')
                ->add($i, ProductType::class, [
                    'required' => false,
                    'placeholder' => null,
                    'enableRemove' => true,
                ]);
        }
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
