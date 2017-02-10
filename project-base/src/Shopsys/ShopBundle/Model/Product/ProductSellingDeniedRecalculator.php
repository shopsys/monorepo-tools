<?php

namespace Shopsys\ShopBundle\Model\Product;

use Doctrine\ORM\EntityManager;

class ProductSellingDeniedRecalculator
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    public function __construct(
        EntityManager $entityManager
    ) {
        $this->em = $entityManager;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     */
    public function calculateSellingDeniedForProduct(Product $product) {
        $products = $this->getProductsForCalculations($product);
        $this->calculate($products);
    }

    public function calculateSellingDeniedForAll() {
        $this->calculate();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product[] $products
     */
    private function calculate(array $products = []) {
        $this->calculateIndependent($products);
        $this->propagateMainVariantSellingDeniedToVariants($products);
        $this->propagateVariantsSellingDeniedToMainVariant($products);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @return \Shopsys\ShopBundle\Model\Product\Product[]
     */
    private function getProductsForCalculations(Product $product) {
        $products = [$product];
        if ($product->isMainVariant()) {
            $products = array_merge($products, $product->getVariants());
        } elseif ($product->isVariant()) {
            $products[] = $product->getMainVariant();
        }

        return $products;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product[] $products
     */
    private function calculateIndependent(array $products) {
        $qb = $this->em->createQueryBuilder()
            ->update(Product::class, 'p')
            ->set('p.calculatedSellingDenied', '
                CASE
                    WHEN p.usingStock = TRUE
                        AND p.stockQuantity <= 0
                        AND p.outOfStockAction = :outOfStockActionExcludeFromSale
                    THEN TRUE
                    ELSE p.sellingDenied
                END
            ')
            ->setParameter('outOfStockActionExcludeFromSale', Product::OUT_OF_STOCK_ACTION_EXCLUDE_FROM_SALE);

        if (count($products) > 0) {
            $qb->andWhere('p IN (:products)')->setParameter('products', $products);
        }
        $qb->getQuery()->execute();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product[] $products
     */
    private function propagateMainVariantSellingDeniedToVariants(array $products) {
        $qb = $this->em->createQueryBuilder()
            ->update(Product::class, 'p')
            ->set('p.calculatedSellingDenied', 'TRUE')
            ->andWhere('p.variantType = :variantTypeVariant')
            ->andWhere('p.calculatedSellingDenied = FALSE')
            ->andWhere(
                'EXISTS (
                    SELECT 1
                    FROM ' . Product::class . ' m
                    WHERE m = p.mainVariant
                        AND m.calculatedSellingDenied = TRUE
                )'
            )
            ->setParameter('variantTypeVariant', Product::VARIANT_TYPE_VARIANT);

        if (count($products) > 0) {
            $qb->andWhere('p IN (:products)')->setParameter('products', $products);
        }
        $qb->getQuery()->execute();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product[] $products
     */
    private function propagateVariantsSellingDeniedToMainVariant(array $products) {
        $qb = $this->em->createQueryBuilder()
            ->update(Product::class, 'p')
            ->set('p.calculatedSellingDenied', 'TRUE')
            ->andWhere('p.variantType = :variantTypeMain')
            ->andWhere('p.calculatedSellingDenied = FALSE')
            ->andWhere(
                'NOT EXISTS (
                    SELECT 1
                    FROM ' . Product::class . ' v
                    WHERE v.mainVariant = p
                        AND v.calculatedSellingDenied = FALSE
                )'
            )
            ->setParameter('variantTypeMain', Product::VARIANT_TYPE_MAIN);

        if (count($products) > 0) {
            $qb->andWhere('p IN (:products)')->setParameter('products', $products);
        }
        $qb->getQuery()->execute();
    }
}
