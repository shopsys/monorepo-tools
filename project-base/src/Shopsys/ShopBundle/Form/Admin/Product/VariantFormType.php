<?php

namespace Shopsys\ShopBundle\Form\Admin\Product;

use Shopsys\ShopBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer;
use Shopsys\ShopBundle\Form\ProductsType;
use Shopsys\ShopBundle\Form\ProductType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class VariantFormType extends AbstractType
{
    const MAIN_VARIANT = 'mainVariant';
    const VARIANTS = 'variants';

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(self::MAIN_VARIANT, ProductType::class, [
                'allow_main_variants' => false,
                'allow_variants' => false,
                'constraints' => [
                    new Constraints\NotBlank(),
                ],
            ])
            ->add(
                $builder
                    ->create(self::VARIANTS, ProductsType::class, [
                        'allow_main_variants' => false,
                        'allow_variants' => false,
                        'constraints' => [
                            new Constraints\NotBlank(),
                        ],
                    ])
                    ->addModelTransformer(new RemoveDuplicatesFromArrayTransformer())
            )
            ->add('save', SubmitType::class);
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
