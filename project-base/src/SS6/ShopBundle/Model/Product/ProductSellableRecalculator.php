<?php

namespace SS6\ShopBundle\Model\Product;

use Doctrine\ORM\EntityManager;

class ProductSellableRecalculator {

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
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 */
	public function calculateSellableForProduct(Product $product) {
		$this->executeQuery($product);
	}

	public function calculateSellableForAll() {
		$this->executeQuery();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product|null $product
	 */
	private function executeQuery(Product $product = null) {
		$qb = $this->em->createQueryBuilder()
			->update(Product::class, 'p')
			->set('p.calculatedSellable', '
				CASE
					WHEN p.usingStock = TRUE
						AND p.stockQuantity <= 0
						AND p.outOfStockAction = :outOfStockActionExcludeFromSale
					THEN FALSE
					ELSE p.sellable
				END
				')
			->setParameter('outOfStockActionExcludeFromSale', Product::OUT_OF_STOCK_ACTION_EXCLUDE_FROM_SALE);

		if ($product !== null) {
			$qb->where('p = :product')->setParameter('product', $product);
		}

		$qb->getQuery()->execute();
	}

}
