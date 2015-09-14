<?php

namespace SS6\ShopBundle\Component\Sitemap;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr\Join;
use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl;
use SS6\ShopBundle\Component\Sitemap\SitemapItem;
use SS6\ShopBundle\Model\Category\CategoryRepository;
use SS6\ShopBundle\Model\Domain\Config\DomainConfig;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Product\ProductRepository;

class SitemapRepository {

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Category\CategoryRepository
	 */
	private $categoryRepository;

	public function __construct(
		ProductRepository $productRepository,
		CategoryRepository $categoryRepository
	) {
		$this->productRepository = $productRepository;
		$this->categoryRepository = $categoryRepository;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Domain\Config\DomainConfig $domainConfig
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return \SS6\ShopBundle\Component\Sitemap\SitemapItem[]
	 */
	public function getSitemapItemsForVisibleProducts(DomainConfig $domainConfig, PricingGroup $pricingGroup) {
		$queryBuilder = $this->productRepository->getAllVisibleQueryBuilder($domainConfig->getId(), $pricingGroup);
		$queryBuilder
			->select('fu.slug')
			->join(FriendlyUrl::class, 'fu', Join::WITH,
				'fu.routeName = :productDetailRouteName
				AND fu.entityId = p.id
				AND fu.domainId = :domainId
				AND fu.main = TRUE'
			)
			->setParameter('productDetailRouteName', 'front_product_detail')
			->setParameter('domainId', $domainConfig->getId());

		$rows = $queryBuilder->getQuery()->execute(null, AbstractQuery::HYDRATE_SCALAR);
		$sitemapItems = [];
		foreach ($rows as $row) {
			$sitemapItem = new SitemapItem();
			$sitemapItem->slug = $row['slug'];
			$sitemapItems[] = $sitemapItem;
		}

		return $sitemapItems;
	}

}
