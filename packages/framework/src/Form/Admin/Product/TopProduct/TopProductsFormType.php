<?php

namespace Shopsys\FrameworkBundle\Form\Admin\Product\TopProduct;

use Shopsys\FrameworkBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer;
use Shopsys\FrameworkBundle\Form\ProductsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TopProductsFormType extends AbstractType
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer
     */
    private $removeDuplicatesTransformer;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer $removeDuplicatesTransformer
     */
    public function __construct(RemoveDuplicatesFromArrayTransformer $removeDuplicatesTransformer)
    {
        $this->removeDuplicatesTransformer = $removeDuplicatesTransformer;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                $builder
                    ->create('products', ProductsType::class, [
                        'required' => false,
                        'sortable' => true,
                    ])
                    ->addViewTransformer($this->removeDuplicatesTransformer)
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
