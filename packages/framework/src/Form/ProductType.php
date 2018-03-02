<?php

namespace Shopsys\FrameworkBundle\Form;

use Shopsys\FrameworkBundle\Component\Transformers\ProductIdToProductTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Transformers\ProductIdToProductTransformer
     */
    private $productIdToProductTransformer;

    public function __construct(
        ProductIdToProductTransformer $productIdToProductTransformer
    ) {
        $this->productIdToProductTransformer = $productIdToProductTransformer;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->productIdToProductTransformer);
    }

    /**
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars['placeholder'] = $options['placeholder'];
        $view->vars['enableRemove'] = $options['enableRemove'];
        $view->vars['allow_main_variants'] = $options['allow_main_variants'];
        $view->vars['allow_variants'] = $options['allow_variants'];

        $product = $form->getData();
        if ($product !== null) {
            /* @var $product \Shopsys\FrameworkBundle\Model\Product\Product */
            $view->vars['productName'] = $product->getName();
        }
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return HiddenType::class;
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'placeholder' => t('Choose product'),
            'enableRemove' => false,
            'required' => true,
            'allow_main_variants' => true,
            'allow_variants' => true,
        ]);
    }
}
