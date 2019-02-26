<?php

namespace Shopsys\FrameworkBundle\Model\Product\Pricing;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\FrameworkBundle\Model\Product\Product;

class ProductCalculatedPriceRepository
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductCalculatedPriceFactoryInterface
     */
    protected $productCalculatedPriceFactory;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductCalculatedPriceFactoryInterface $productCalculatedPriceFactory
     */
    public function __construct(EntityManagerInterface $em, ProductCalculatedPriceFactoryInterface $productCalculatedPriceFactory)
    {
        $this->em = $em;
        $this->productCalculatedPriceFactory = $productCalculatedPriceFactory;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getProductCalculatedPriceRepository()
    {
        return $this->em->getRepository(ProductCalculatedPrice::class);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @param \Shopsys\FrameworkBundle\Component\Money\Money|null $priceWithVat
     */
    public function saveCalculatedPrice(Product $product, PricingGroup $pricingGroup, ?Money $priceWithVat)
    {
        /** @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductCalculatedPrice $productCalculatedPrice */
        $productCalculatedPrice = $this->getProductCalculatedPriceRepository()->find([
            'product' => $product->getId(),
            'pricingGroup' => $pricingGroup->getId(),
        ]);

        if ($productCalculatedPrice === null) {
            $productCalculatedPrice = $this->productCalculatedPriceFactory->create($product, $pricingGroup, $priceWithVat);
            $this->em->persist($productCalculatedPrice);
        } else {
            $productCalculatedPrice->setPriceWithVat($priceWithVat);
        }

        $this->em->flush($productCalculatedPrice);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     */
    public function createProductCalculatedPricesForPricingGroup(PricingGroup $pricingGroup)
    {
        $query = $this->em->createNativeQuery(
            'INSERT INTO product_calculated_prices (product_id, pricing_group_id, price_with_vat)
            SELECT id, :pricingGroupId, :priceWithVat FROM products',
            new ResultSetMapping()
        );
        $query->execute([
            'pricingGroupId' => $pricingGroup->getId(),
            'priceWithVat' => null,
        ]);
    }
}
