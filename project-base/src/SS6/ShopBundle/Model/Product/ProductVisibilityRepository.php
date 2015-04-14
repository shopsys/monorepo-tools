<?php

namespace SS6\ShopBundle\Model\Product;

use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupRepository;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductVisibility;

class ProductVisibilityRepository {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroupRepository
	 */
	private $pricingGroupRepository;

	public function __construct(
		EntityManager $em,
		Domain $domain,
		PricingGroupRepository $pricingGroupRepository
	) {
		$this->em = $em;
		$this->domain = $domain;
		$this->pricingGroupRepository = $pricingGroupRepository;
	}

	public function refreshProductsVisibility() {
		$this->refreshProductVisibility();
		$this->refreshGlobalProductVisibility();
	}

	private function refreshGlobalProductVisibility() {
		$query = $this->em->createNativeQuery('UPDATE products AS p
			SET visible = (p.hidden = FALSE) AND EXISTS(
					SELECT 1
					FROM product_visibilities AS pv
					WHERE pv.product_id = p.id
						AND pv.visible = TRUE
				)', new ResultSetMapping());
		$query->execute();
	}

	/**
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	private function refreshProductVisibility() {
		$now = new DateTime();

		foreach ($this->pricingGroupRepository->getAll() as $pricingGroup) {
			$domain = $this->domain->getDomainConfigById($pricingGroup->getDomainId());
			$query = $this->em->createNativeQuery('UPDATE product_visibilities AS pv
					SET visible = CASE
							WHEN (
								p.hidden = FALSE
								AND
								pd.hidden = FALSE
								AND
								(p.selling_from IS NULL OR p.selling_from <= :now)
								AND
								(p.selling_to IS NULL OR p.selling_to >= :now)
								AND EXISTS (
									SELECT 1
									FROM product_calculated_prices as pcp
									WHERE pcp.price_with_vat > 0
										AND pcp.product_id = pv.product_id
										AND pcp.pricing_group_id = pv.pricing_group_id
								)
								AND EXISTS (
									SELECT 1
									FROM product_translations AS pt
									WHERE pt.translatable_id = pv.product_id
										AND pt.locale = :locale
										AND pt.name IS NOT NULL
								)
								AND EXISTS (
									SELECT 1
									FROM product_categories AS pc
									JOIN category_domains AS cd ON cd.category_id = pc.category_id
										AND cd.domain_id = pv.domain_id
									WHERE pc.product_id = p.id
										AND cd.visible = TRUE
								)
							)
							THEN TRUE
							ELSE FALSE
						END

					FROM products AS p
					JOIN product_domains AS pd ON pd.product_id = p.id
					WHERE p.id = pv.product_id
						AND pv.domain_id = :domainId
						AND pv.domain_id = pd.domain_id
						AND pv.pricing_group_id = :pricingGroupId', new ResultSetMapping());

				$query->execute([
					'now' => $now,
					'locale' => $domain->getLocale(),
					'domainId' => $domain->getId(),
					'priceCalculationType' => Product::PRICE_CALCULATION_TYPE_MANUAL,
					'pricingGroupId' => $pricingGroup->getId(),
				]);
		}
	}

	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	private function getProductVisibilityRepository() {
		return $this->em->getRepository(ProductVisibility::class);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Product\ProductVisibility|null
	 */
	public function findProductVisibility(
		Product $product,
		PricingGroup $pricingGroup,
		$domainId
	) {
		return $this->getProductVisibilityRepository()->find([
			'product' => $product->getId(),
			'pricingGroup' => $pricingGroup->getId(),
			'domainId' => $domainId,
		]);
	}

}
