<?php

namespace Shopsys\ShopBundle\Component\Sitemap;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl;
use Shopsys\ShopBundle\Component\Sitemap\SitemapItem;
use Shopsys\ShopBundle\Model\Article\ArticleRepository;
use Shopsys\ShopBundle\Model\Category\CategoryRepository;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\ShopBundle\Model\Product\ProductRepository;

class SitemapRepository
{
    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductRepository
     */
    private $productRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Category\CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Article\ArticleRepository
     */
    private $articleRepository;

    public function __construct(
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        ArticleRepository $articleRepository
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->articleRepository = $articleRepository;
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return \Shopsys\ShopBundle\Component\Sitemap\SitemapItem[]
     */
    public function getSitemapItemsForVisibleProducts(DomainConfig $domainConfig, PricingGroup $pricingGroup)
    {
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

        return $this->getSitemapItemsFromQueryBuilderWithSlugField($queryBuilder);
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return \Shopsys\ShopBundle\Component\Sitemap\SitemapItem[]
     */
    public function getSitemapItemsForVisibleCategories(DomainConfig $domainConfig)
    {
        $queryBuilder = $this->categoryRepository->getAllVisibleByDomainIdQueryBuilder($domainConfig->getId());
        $queryBuilder
            ->select('fu.slug')
            ->join(FriendlyUrl::class, 'fu', Join::WITH,
                'fu.routeName = :productListRouteName
                AND fu.entityId = c.id
                AND fu.domainId = :domainId
                AND fu.main = TRUE'
            )
            ->setParameter('productListRouteName', 'front_product_list')
            ->setParameter('domainId', $domainConfig->getId());

        return $this->getSitemapItemsFromQueryBuilderWithSlugField($queryBuilder);
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return \Shopsys\ShopBundle\Component\Sitemap\SitemapItem[]
     */
    public function getSitemapItemsForArticlesOnDomain(DomainConfig $domainConfig)
    {
        $queryBuilder = $this->articleRepository->getVisibleArticlesByDomainIdQueryBuilder($domainConfig->getId());
        $queryBuilder
            ->select('fu.slug')
            ->join(FriendlyUrl::class, 'fu', Join::WITH,
                'fu.routeName = :articleDetailRouteName
                AND fu.entityId = a.id
                AND fu.domainId = :domainId
                AND fu.main = TRUE'
            )
            ->setParameter('articleDetailRouteName', 'front_article_detail')
            ->setParameter('domainId', $domainConfig->getId());

        return $this->getSitemapItemsFromQueryBuilderWithSlugField($queryBuilder);
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @return \Shopsys\ShopBundle\Component\Sitemap\SitemapItem[]
     */
    private function getSitemapItemsFromQueryBuilderWithSlugField(QueryBuilder $queryBuilder)
    {
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
