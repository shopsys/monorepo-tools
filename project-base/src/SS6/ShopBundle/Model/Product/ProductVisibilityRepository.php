<?php

namespace SS6\ShopBundle\Model\Product;

use DateTime;
use Doctrine\ORM\EntityManager;

class ProductVisibilityRepository {
	/** 
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

	public function refreshProductsVisibility() {
		$now = new DateTime();
		
		$query = $this->em->createQuery(sprintf('UPDATE %s p
				SET p.visible = CASE
						WHEN (
							p.hidden = FALSE
							AND
							(p.sellingFrom IS NULL OR p.sellingFrom <= :now)
							AND
							(p.sellingTo IS NULL OR p.sellingTo >= :now)
						)
						THEN TRUE
						ELSE FALSE
					END', Product::class));
		$query->execute(array('now' => $now));
	}
}
