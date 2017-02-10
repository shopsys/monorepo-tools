<?php

namespace Shopsys\ShopBundle\Form;

use Shopsys\ShopBundle\Component\Transformers\ProductIdToProductTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductType extends AbstractType {

    /**
     * @var \Shopsys\ShopBundle\Component\Transformers\ProductIdToProductTransformer
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
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->addModelTransformer($this->productIdToProductTransformer);
    }

    /**
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options) {
        parent::buildView($view, $form, $options);

        $view->vars['placeholder'] = $options['placeholder'];
        $view->vars['enableRemove'] = $options['enableRemove'];
        $view->vars['allow_main_variants'] = $options['allow_main_variants'];
        $view->vars['allow_variants'] = $options['allow_variants'];

        $product = $form->getData();
        if ($product !== null) {
            /* @var $product \Shopsys\ShopBundle\Model\Product\Product */
            $view->vars['productName'] = $product->getName();
        }
    }

    /**
     * @return string
     */
    public function getParent() {
        return 'hidden';
    }

    /**
     * @return string
     */
    public function getName() {
        return 'product';
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults([
            'placeholder' => t('Choose product'),
            'enableRemove' => false,
            'required' => true,
            'allow_main_variants' => true,
            'allow_variants' => true,
        ]);
    }

}
