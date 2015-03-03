<?php

namespace SS6\ShopBundle\Model\Product;

use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use SS6\ShopBundle\Model\Domain\Domain;

class ProductVisibilityRepository {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	public function __construct(
		EntityManager $em,
		Domain $domain
	) {
		$this->em = $em;
		$this->domain = $domain;
	}

	public function refreshProductsVisibility() {
		$this->refreshProductDomainsVisibility();
		$this->refreshGlobalProductVisibility();
	}

	private function refreshGlobalProductVisibility() {
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

		$domains = $this->domain->getAll();

		foreach ($domains as $domain) {
			$query = $this->em->createNativeQuery('UPDATE product_domains AS pd
					SET visible = CASE
							WHEN (
								p.hidden = FALSE
								AND
								pd.hidden = FALSE
								AND
								(p.selling_from IS NULL OR p.selling_from <= :now)
								AND
								(p.selling_to IS NULL OR p.selling_to >= :now)
								AND
								(p.price > 0 OR p.price_calculation_type = :priceCalculationType)
								AND EXISTS (
									SELECT 1
									FROM product_translations AS pt
									WHERE pt.translatable_id = pd.product_id
										AND pt.locale = :locale
										AND pt.name IS NOT NULL
								)
							)
							THEN TRUE
							ELSE FALSE
						END

				FROM products AS p
				WHERE p.id = pd.product_id
					AND pd.domain_id = :domainId', new ResultSetMapping());

			/**
			 * temporary solution -
			 * when product price calculation type is set to manual, all input prices must be filled and greater than 0
			 */
			$query->execute([
				'now' => $now,
				'locale' => $domain->getLocale(),
				'domainId' => $domain->getId(),
				'priceCalculationType' => Product::PRICE_CALCULATION_TYPE_MANUAL,
			]);
		}
	}

}
