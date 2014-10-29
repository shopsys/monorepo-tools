<?php

namespace SS6\ShopBundle\Model\Product;

use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;

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
		$this->refreshProductDomainsVisibility();

		$query = $this->em->createNativeQuery('UPDATE products AS p
				SET visible = (p.hidden = FALSE) AND EXISTS(
						SELECT 1
						FROM product_domains AS pd
						WHERE pd.product_id = p.id
							AND pd.visible = TRUE
					)', new ResultSetMapping());
		$query->execute();
	}

	private function refreshProductDomainsVisibility() {
		$now = new DateTime();

		$query = $this->em->createNativeQuery('UPDATE product_domains AS pd
				SET visible = CASE
						WHEN (
							pd.show = TRUE
							AND
							(p.selling_from IS NULL OR p.selling_from <= :now)
							AND
							(p.selling_to IS NULL OR p.selling_to >= :now)
							AND
							p.price > 0
						)
						THEN TRUE
						ELSE FALSE
					END

			FROM products AS p
			WHERE p.id = pd.product_id', new ResultSetMapping());
		$query->execute(array('now' => $now));
	}

}
