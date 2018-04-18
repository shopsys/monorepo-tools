<?php

namespace Shopsys\FrameworkBundle\Model\Product\Pricing;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\FrameworkBundle\Model\Product\Product;

class ProductManualInputPriceFacade
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductManualInputPriceRepository
     */
    private $productManualInputPriceRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductManualInputPriceService
     */
    private $productManualInputPriceService;

    public function __construct(
        EntityManagerInterface $em,
        ProductManualInputPriceRepository $productManualInputPriceRepository,
        ProductManualInputPriceService $productManualInputPriceService
    ) {
        $this->em = $em;
        $this->productManualInputPriceRepository = $productManualInputPriceRepository;
        $this->productManualInputPriceService = $productManualInputPriceService;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @param string $inputPrice
     */
    public function refresh(Product $product, PricingGroup $pricingGroup, $inputPrice)
    {
        $manualInputPrice = $this->productManualInputPriceRepository->findByProductAndPricingGroup($product, $pricingGroup);
        $refreshedProductManualInputPrice = $this->productManualInputPriceService->refresh(
            $product,
            $pricingGroup,
            $inputPrice,
            $manualInputPrice
        );
        $this->em->persist($refreshedProductManualInputPrice);
        $this->em->flush($refreshedProductManualInputPrice);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductManualInputPrice[]
     */
    public function getAllByProduct(Product $product)
    {
        return $this->productManualInputPriceRepository->getByProduct($product);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     */
    public function deleteByProduct(Product $product)
    {
        $manualInputPrices = $this->productManualInputPriceRepository->getByProduct($product);
        foreach ($manualInputPrices as $manualInputPrice) {
            $this->em->remove($manualInputPrice);
        }
        $this->em->flush($manualInputPrices);
    }
}
