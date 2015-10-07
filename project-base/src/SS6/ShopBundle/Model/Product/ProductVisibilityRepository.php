<?php

namespace SS6\ShopBundle\Model\Product;

use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use SS6\ShopBundle\Component\Domain\Domain;
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
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroupRepository
	 */
	private $pricingGroupRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	public function __construct(
		EntityManager $em,
		Domain $domain,
		PricingGroupRepository $pricingGroupRepository,
		ProductRepository $productRepository
	) {
		$this->em = $em;
		$this->domain = $domain;
		$this->pricingGroupRepository = $pricingGroupRepository;
		$this->productRepository = $productRepository;
	}

	/**
	 * @param bool $onlyMarkedProducts
	 */
	public function refreshProductsVisibility($onlyMarkedProducts = false) {
		$this->calculateIndependentVisibility($onlyMarkedProducts);
		$this->propagateMainVariantVisibilityToVariants($onlyMarkedProducts);
		$this->propagateVariantsVisibilityToMainVariant($onlyMarkedProducts);
		$this->refreshGlobalProductVisibility();
		$this->markAllProductsVisibilityAsRecalculated();
	}

	private function refreshGlobalProductVisibility() {
		$query = $this->em->createNativeQuery('UPDATE products AS p
			SET visible = (p.calculated_hidden = FALSE) AND EXISTS(
					SELECT 1
					FROM product_visibilities AS pv
					WHERE pv.product_id = p.id
						AND pv.visible = TRUE
				)', new ResultSetMapping());
		$query->execute();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 */
	public function createAndRefreshProductVisibilitiesForPricingGroup(PricingGroup $pricingGroup) {
		$query = $this->em->createNativeQuery('INSERT INTO product_visibilities (product_id, pricing_group_id, domain_id, visible)
			SELECT id, :pricingGroupId, :domainId, :visible FROM products', new ResultSetMapping());
		$query->execute([
			'pricingGroupId' => $pricingGroup->getId(),
			'domainId' => $pricingGroup->getDomainId(),
			'visible' => false,
		]);
		$this->refreshProductsVisibility();
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
	 * @return \SS6\ShopBundle\Model\Product\ProductVisibility
	 */
	public function getProductVisibility(
		Product $product,
		PricingGroup $pricingGroup,
		$domainId
	) {
		$productVisibility = $this->getProductVisibilityRepository()->find([
			'product' => $product->getId(),
			'pricingGroup' => $pricingGroup->getId(),
			'domainId' => $domainId,
		]);
		if ($productVisibility === null) {
			throw new \SS6\ShopBundle\Model\Product\Exception\ProducVisibilitytNotFoundException();
		}

		return $productVisibility;
	}

	private function markAllProductsVisibilityAsRecalculated() {
		$this->em->createNativeQuery('UPDATE products SET recalculate_visibility = FALSE', new ResultSetMapping())
			->execute();
	}

	/**
	 * @param bool $onlyMarkedProducts
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	private function calculateIndependentVisibility($onlyMarkedProducts) {
		$now = new DateTime();
		if ($onlyMarkedProducts) {
			$onlyMarkedProductsCondition = ' AND p.recalculate_visibility = TRUE';
		} else {
			$onlyMarkedProductsCondition = '';
		}

		$query = $this->em->createNativeQuery('
			UPDATE product_visibilities AS pv
			SET visible = CASE
					WHEN (
						p.calculated_hidden = FALSE
						AND
						(p.selling_from IS NULL OR p.selling_from <= :now)
						AND
						(p.selling_to IS NULL OR p.selling_to >= :now)
						AND
						(
							p.variant_type = :variantTypeMain
							OR
							EXISTS (
								SELECT 1
								FROM product_calculated_prices as pcp
								WHERE pcp.price_with_vat > 0
									AND pcp.product_id = pv.product_id
									AND pcp.pricing_group_id = pv.pricing_group_id
							)
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
							FROM product_category_domains AS pcd
							JOIN category_domains AS cd ON cd.category_id = pcd.category_id
								AND cd.domain_id = pcd.domain_id
							WHERE pcd.product_id = p.id
								AND pcd.domain_id = pv.domain_id
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
				AND pv.pricing_group_id = :pricingGroupId '
			. $onlyMarkedProductsCondition, new ResultSetMapping());

		foreach ($this->pricingGroupRepository->getAll() as $pricingGroup) {
			$domain = $this->domain->getDomainConfigById($pricingGroup->getDomainId());
			$query->execute([
				'now' => $now,
				'locale' => $domain->getLocale(),
				'domainId' => $domain->getId(),
				'priceCalculationType' => Product::PRICE_CALCULATION_TYPE_MANUAL,
				'pricingGroupId' => $pricingGroup->getId(),
				'variantTypeMain' => Product::VARIANT_TYPE_MAIN,
			]);
		}
	}

	/**
	 * @param bool $onlyMarkedProducts
	 */
	private function propagateMainVariantVisibilityToVariants($onlyMarkedProducts) {
		if ($onlyMarkedProducts) {
			$onlyMarkedProductsCondition = ' AND p.recalculate_visibility = TRUE';
		} else {
			$onlyMarkedProductsCondition = '';
		}

		$query = $this->em->createNativeQuery('
			UPDATE product_visibilities AS pv
			SET visible = FALSE
			FROM products AS p
			WHERE p.id = pv.product_id
				AND p.variant_type = :variantTypeVariant
				AND pv.visible = TRUE
				AND EXISTS (
					SELECT 1
					FROM product_visibilities mpv
					WHERE mpv.product_id = p.main_variant_id
						AND mpv.domain_id = pv.domain_id
						AND mpv.pricing_group_id = pv.pricing_group_id
						AND mpv.visible = FALSE
				) '
			. $onlyMarkedProductsCondition, new ResultSetMapping());

		$query->execute([
			'variantTypeVariant' => Product::VARIANT_TYPE_VARIANT,
		]);
	}

	/**
	 * @param bool $onlyMarkedProducts
	 */
	private function propagateVariantsVisibilityToMainVariant($onlyMarkedProducts) {
		if ($onlyMarkedProducts) {
			$onlyMarkedProductsCondition = ' AND p.recalculate_visibility = TRUE';
		} else {
			$onlyMarkedProductsCondition = '';
		}

		$query = $this->em->createNativeQuery('UPDATE product_visibilities AS pv
			SET visible = FALSE
			FROM products AS p
			WHERE p.id = pv.product_id
				AND p.variant_type = :variantTypeMain
				AND pv.visible = TRUE
				AND NOT EXISTS (
					SELECT 1
					FROM products vp
					JOIN product_visibilities vpv ON
						vpv.product_id = vp.id
						AND vpv.domain_id = pv.domain_id
						AND vpv.pricing_group_id = pv.pricing_group_id
					WHERE vp.main_variant_id = p.id
						AND vpv.visible = TRUE
				) '
			. $onlyMarkedProductsCondition, new ResultSetMapping());

		$query->execute([
			'variantTypeMain' => Product::VARIANT_TYPE_MAIN,
		]);
	}

}
