<?php

namespace Shopsys\FrameworkBundle\Model\Product;

use Shopsys\FrameworkBundle\Model\Pricing\BasePriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\InputPriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\PricingSetting;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;

class ProductService
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\InputPriceCalculation
     */
    private $inputPriceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\BasePriceCalculation
     */
    private $basePriceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\PricingSetting
     */
    private $pricingSetting;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler
     */
    private $productPriceRecalculationScheduler;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomainFactoryInterface
     */
    protected $productCategoryDomainFactory;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\InputPriceCalculation $inputPriceCalculation
     * @param \Shopsys\FrameworkBundle\Model\Pricing\BasePriceCalculation $basePriceCalculation
     * @param \Shopsys\FrameworkBundle\Model\Pricing\PricingSetting $pricingSetting
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler $productPriceRecalculationScheduler
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomainFactoryInterface $productCategoryDomainFactory
     */
    public function __construct(
        InputPriceCalculation $inputPriceCalculation,
        BasePriceCalculation $basePriceCalculation,
        PricingSetting $pricingSetting,
        ProductPriceRecalculationScheduler $productPriceRecalculationScheduler,
        ProductCategoryDomainFactoryInterface $productCategoryDomainFactory
    ) {
        $this->inputPriceCalculation = $inputPriceCalculation;
        $this->basePriceCalculation = $basePriceCalculation;
        $this->pricingSetting = $pricingSetting;
        $this->productPriceRecalculationScheduler = $productPriceRecalculationScheduler;
        $this->productCategoryDomainFactory = $productCategoryDomainFactory;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductManualInputPrice[] $productManualInputPrices
     * @param string $newVatPercent
     */
    public function recalculateInputPriceForNewVatPercent(Product $product, $productManualInputPrices, $newVatPercent)
    {
        $inputPriceType = $this->pricingSetting->getInputPriceType();

        foreach ($productManualInputPrices as $productManualInputPrice) {
            $basePriceForPricingGroup = $this->basePriceCalculation->calculateBasePrice(
                $productManualInputPrice->getInputPrice(),
                $inputPriceType,
                $product->getVat()
            );
            $inputPriceForPricingGroup = $this->inputPriceCalculation->getInputPrice(
                $inputPriceType,
                $basePriceForPricingGroup->getPriceWithVat(),
                $newVatPercent
            );
            $productManualInputPrice->setInputPrice($inputPriceForPricingGroup);
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductData $productData
     */
    public function edit(Product $product, ProductData $productData)
    {
        $product->edit($this->productCategoryDomainFactory, $productData);
        $this->productPriceRecalculationScheduler->scheduleProductForImmediateRecalculation($product);
        $this->markProductForVisibilityRecalculation($product);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat $vat
     */
    public function changeVat(Product $product, Vat $vat)
    {
        $product->changeVat($vat);
        $this->productPriceRecalculationScheduler->scheduleProductForImmediateRecalculation($product);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return \Shopsys\FrameworkBundle\Model\Product\ProductDeleteResult
     */
    public function delete(Product $product)
    {
        if ($product->isMainVariant()) {
            foreach ($product->getVariants() as $variantProduct) {
                $variantProduct->unsetMainVariant();
            }
        }
        if ($product->isVariant()) {
            return new ProductDeleteResult([$product->getMainVariant()]);
        }

        return new ProductDeleteResult();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     */
    public function markProductForVisibilityRecalculation(Product $product)
    {
        $product->markForVisibilityRecalculation();
        if ($product->isMainVariant()) {
            foreach ($product->getVariants() as $variant) {
                $variant->markForVisibilityRecalculation();
            }
        } elseif ($product->isVariant()) {
            $product->getMainVariant()->markForVisibilityRecalculation();
        }
    }
}
