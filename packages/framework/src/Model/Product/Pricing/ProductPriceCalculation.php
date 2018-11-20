<?php

namespace Shopsys\FrameworkBundle\Model\Product\Pricing;

use Shopsys\FrameworkBundle\Model\Pricing\BasePriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\FrameworkBundle\Model\Pricing\PricingService;
use Shopsys\FrameworkBundle\Model\Pricing\PricingSetting;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductRepository;

class ProductPriceCalculation
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\BasePriceCalculation
     */
    private $basePriceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\PricingSetting
     */
    private $pricingSetting;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductManualInputPriceRepository
     */
    private $productManualInputPriceRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductRepository
     */
    private $productRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\PricingService
     */
    private $pricingService;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\BasePriceCalculation $basePriceCalculation
     * @param \Shopsys\FrameworkBundle\Model\Pricing\PricingSetting $pricingSetting
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductManualInputPriceRepository $productManualInputPriceRepository
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductRepository $productRepository
     * @param \Shopsys\FrameworkBundle\Model\Pricing\PricingService $pricingService
     */
    public function __construct(
        BasePriceCalculation $basePriceCalculation,
        PricingSetting $pricingSetting,
        ProductManualInputPriceRepository $productManualInputPriceRepository,
        ProductRepository $productRepository,
        PricingService $pricingService
    ) {
        $this->pricingSetting = $pricingSetting;
        $this->basePriceCalculation = $basePriceCalculation;
        $this->productManualInputPriceRepository = $productManualInputPriceRepository;
        $this->productRepository = $productRepository;
        $this->pricingService = $pricingService;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param int $domainId
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPrice
     */
    public function calculatePrice(Product $product, $domainId, PricingGroup $pricingGroup)
    {
        if ($product->isMainVariant()) {
            return $this->calculateMainVariantPrice($product, $domainId, $pricingGroup);
        }

        return $this->calculateProductPriceForPricingGroup($product, $pricingGroup);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $mainVariant
     * @param int $domainId
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPrice
     */
    private function calculateMainVariantPrice(Product $mainVariant, $domainId, PricingGroup $pricingGroup)
    {
        $variants = $this->productRepository->getAllSellableVariantsByMainVariant(
            $mainVariant,
            $domainId,
            $pricingGroup
        );
        if (count($variants) === 0) {
            $message = 'Main variant ID = ' . $mainVariant->getId() . ' has no sellable variants.';
            throw new \Shopsys\FrameworkBundle\Model\Product\Pricing\Exception\MainVariantPriceCalculationException($message);
        }

        $variantPrices = [];
        foreach ($variants as $variant) {
            $variantPrices[] = $this->calculatePrice($variant, $domainId, $pricingGroup);
        }

        $minVariantPrice = $this->pricingService->getMinimumPriceByPriceWithoutVat($variantPrices);
        $from = $this->pricingService->arePricesDifferent($variantPrices);

        return new ProductPrice($minVariantPrice, $from);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPrice
     */
    private function calculateProductPriceForPricingGroup(Product $product, PricingGroup $pricingGroup)
    {
        $manualInputPrice = $this->productManualInputPriceRepository->findByProductAndPricingGroup($product, $pricingGroup);
        if ($manualInputPrice !== null) {
            $inputPrice = $manualInputPrice->getInputPrice();
        } else {
            $inputPrice = 0;
        }

        $basePrice = $this->basePriceCalculation->calculateBasePrice(
            $inputPrice,
            $this->pricingSetting->getInputPriceType(),
            $product->getVat()
        );

        return new ProductPrice($basePrice, false);
    }
}
