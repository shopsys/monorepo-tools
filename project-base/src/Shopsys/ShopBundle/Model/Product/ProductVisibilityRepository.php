<?php

namespace Shopsys\ShopBundle\Model\Product;

use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\ResultSetMapping;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Model\Category\Category;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupRepository;
use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Product\ProductVisibility;

class ProductVisibilityRepository {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \Shopsys\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupRepository
	 */
	private $pricingGroupRepository;

	/**
	 * @var \Shopsys\ShopBundle\Model\Product\ProductRepository
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
		$this->hideVariantsWithInvisibleMainVariant($onlyMarkedProducts);
		$this->hideMainVariantsWithoutVisibleVariants($onlyMarkedProducts);
		$this->refreshGlobalProductVisibility($onlyMarkedProducts);
		$this->markAllProductsVisibilityAsRecalculated();
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Category\Category $category
	 */
	public function markProductsForRecalculationAffectedByCategory(Category $category) {
		$affectedProductsDql = $this->em->createQueryBuilder()
			->select('IDENTITY(pcd.product)')
			->from(ProductCategoryDomain::class, 'pcd')
			->join(Category::class, 'c', Join::WITH, 'c = pcd.category AND c.lft >= :lft AND c.rgt <= :rgt')
			->getDQL();

		$this->em->createQueryBuilder()
			->update(Product::class, 'p')
			->set('p.recalculateVisibility', 'TRUE')
			->where('p.recalculateVisibility = FALSE')
			->andWhere('p IN (' . $affectedProductsDql . ')')
			->setParameters([
				'lft' => $category->getLft(),
				'rgt' => $category->getRgt(),
			])
			->getQuery()
			->execute();
	}

	/**
	 * @param bool $onlyMarkedProducts
	 */
	private function refreshGlobalProductVisibility($onlyMarkedProducts) {
		if ($onlyMarkedProducts) {
			$onlyMarkedProductsWhereClause = ' WHERE p.recalculate_visibility = TRUE';
		} else {
			$onlyMarkedProductsWhereClause = '';
		}

		$query = $this->em->createNativeQuery('
			UPDATE products AS p
			SET calculated_visibility = (p.calculated_hidden = FALSE) AND EXISTS(
					SELECT 1
					FROM product_visibilities AS pv
					WHERE pv.product_id = p.id
						AND pv.visible = TRUE
				)
			' . $onlyMarkedProductsWhereClause,
			new ResultSetMapping());
		$query->execute();
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @param int $domainId
	 */
	public function createAndRefreshProductVisibilitiesForPricingGroup(PricingGroup $pricingGroup, $domainId) {
		$query = $this->em->createNativeQuery('INSERT INTO product_visibilities (product_id, pricing_group_id, domain_id, visible)
			SELECT id, :pricingGroupId, :domainId, :calculatedVisibility FROM products', new ResultSetMapping());
		$query->execute([
			'pricingGroupId' => $pricingGroup->getId(),
			'domainId' => $domainId,
			'calculatedVisibility' => false,
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
	 * @param \Shopsys\ShopBundle\Model\Product\Product $product
	 * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @param int $domainId
	 * @return \Shopsys\ShopBundle\Model\Product\ProductVisibility
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
			throw new \Shopsys\ShopBundle\Model\Product\Exception\ProductVisibilityNotFoundException();
		}

		return $productVisibility;
	}

	private function markAllProductsVisibilityAsRecalculated() {
		$this->em->createNativeQuery(
			'UPDATE products SET recalculate_visibility = FALSE WHERE recalculate_visibility = TRUE',
			new ResultSetMapping()
		)->execute();
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
				'pricingGroupId' => $pricingGroup->getId(),
				'variantTypeMain' => Product::VARIANT_TYPE_MAIN,
			]);
		}
	}

	/**
	 * @param bool $onlyMarkedProducts
	 */
	private function hideVariantsWithInvisibleMainVariant($onlyMarkedProducts) {
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
	private function hideMainVariantsWithoutVisibleVariants($onlyMarkedProducts) {
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
