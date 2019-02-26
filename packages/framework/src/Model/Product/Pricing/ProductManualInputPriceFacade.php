<?php

namespace Shopsys\FrameworkBundle\Model\Product\Pricing;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\FrameworkBundle\Model\Product\Product;

class ProductManualInputPriceFacade
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductManualInputPriceRepository
     */
    protected $productManualInputPriceRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductManualInputPriceFactoryInterface
     */
    protected $productManualInputPriceFactory;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductManualInputPriceRepository $productManualInputPriceRepository
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductManualInputPriceFactoryInterface $productManualInputPriceFactory
     */
    public function __construct(
        EntityManagerInterface $em,
        ProductManualInputPriceRepository $productManualInputPriceRepository,
        ProductManualInputPriceFactoryInterface $productManualInputPriceFactory
    ) {
        $this->em = $em;
        $this->productManualInputPriceRepository = $productManualInputPriceRepository;
        $this->productManualInputPriceFactory = $productManualInputPriceFactory;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @param \Shopsys\FrameworkBundle\Component\Money\Money|null $inputPrice
     */
    public function refresh(Product $product, PricingGroup $pricingGroup, ?Money $inputPrice)
    {
        $manualInputPrice = $this->productManualInputPriceRepository->findByProductAndPricingGroup($product, $pricingGroup);
        if ($manualInputPrice === null) {
            $manualInputPrice = $this->productManualInputPriceFactory->create($product, $pricingGroup, $inputPrice);
        } else {
            $manualInputPrice->setInputPrice($inputPrice);
        }
        $this->em->persist($manualInputPrice);
        $this->em->flush($manualInputPrice);
    }
}
