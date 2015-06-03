<?php

namespace SS6\ShopBundle\Model\Product;

use Doctrine\ORM\EntityManager;

class ProductHiddenRecalculator {

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
	public function calculateHiddenForProduct(Product $product) {
		$this->executeQuery($product);
	}

	public function calculateHiddenForAll() {
		$this->executeQuery();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product|null $product
	 */
	private function executeQuery(Product $product = null) {
		$qb = $this->em->createQueryBuilder()
			->update(Product::class, 'p')
			->set('p.calculatedHidden', '
				CASE
					WHEN p.usingStock = TRUE
						AND p.stockQuantity <= 0
						AND p.outOfStockAction = :outOfStockActionHide
					THEN TRUE
					ELSE p.hidden
				END
				')
			->setParameter('outOfStockActionHide', Product::OUT_OF_STOCK_ACTION_HIDE);

		if ($product !== null) {
			$qb->where('p = :product')->setParameter('product', $product);
		}

		$qb->getQuery()->execute();
	}

}
