<?php

namespace Shopsys\FrameworkBundle\Form;

use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade;
use Shopsys\FrameworkBundle\Model\Product\Pricing\AdminProductPriceCalculationFacade;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductFacade;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductCalculatedPricesType extends AbstractType
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\AdminProductPriceCalculationFacade
     */
    private $adminProductPriceCalculationFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade
     */
    private $pricingGroupFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductFacade
     */
    private $productFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\AdminProductPriceCalculationFacade $adminProductPriceCalculationFacade
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade $pricingGroupFacade
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductFacade $productFacade
     */
    public function __construct(
        AdminProductPriceCalculationFacade $adminProductPriceCalculationFacade,
        PricingGroupFacade $pricingGroupFacade,
        ProductFacade $productFacade
    ) {
        $this->adminProductPriceCalculationFacade = $adminProductPriceCalculationFacade;
        $this->pricingGroupFacade = $pricingGroupFacade;
        $this->productFacade = $productFacade;
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('product')
            ->setAllowedTypes('product', [Product::class, 'null']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $product = $options['product'];

        if ($product !== null) {
            if ($product->getPriceCalculationType() === Product::PRICE_CALCULATION_TYPE_AUTO) {
                $view->vars['productBasePrice'] = $this->adminProductPriceCalculationFacade->calculateProductBasePrice($product);
            }

            try {
                $productSellingPricesIndexedByDomainId = $this->productFacade->getAllProductSellingPricesIndexedByDomainId($product);
                $view->vars['productSellingPricesIndexedByDomainId'] = $productSellingPricesIndexedByDomainId;
            } catch (\Shopsys\FrameworkBundle\Model\Product\Pricing\Exception\MainVariantPriceCalculationException $ex) {
            }
        } else {
            $view->vars['pricingGroupsIndexedByDomainId'] = $this->pricingGroupFacade->getAllIndexedByDomainId();
        }
    }
}
