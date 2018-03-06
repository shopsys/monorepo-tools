<?php

namespace Shopsys\FrameworkBundle\Component\Sitemap;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrl;
use Shopsys\FrameworkBundle\Model\Article\ArticleRepository;
use Shopsys\FrameworkBundle\Model\Category\CategoryRepository;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\FrameworkBundle\Model\Product\ProductRepository;

class SitemapRepository
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductRepository
     */
    private $productRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Article\ArticleRepository
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
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return \Shopsys\FrameworkBundle\Component\Sitemap\SitemapItem[]
     */
    public function getSitemapItemsForVisibleProducts(DomainConfig $domainConfig, PricingGroup $pricingGroup)
    {
        $queryBuilder = $this->productRepository->getAllVisibleQueryBuilder($domainConfig->getId(), $pricingGroup);
        $queryBuilder
            ->select('fu.slug')
            ->join(
                FriendlyUrl::class,
                'fu',
                Join::WITH,
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
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return \Shopsys\FrameworkBundle\Component\Sitemap\SitemapItem[]
     */
    public function getSitemapItemsForVisibleCategories(DomainConfig $domainConfig)
    {
        $queryBuilder = $this->categoryRepository->getAllVisibleByDomainIdQueryBuilder($domainConfig->getId());
        $queryBuilder
            ->select('fu.slug')
            ->join(
                FriendlyUrl::class,
                'fu',
                Join::WITH,
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
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return \Shopsys\FrameworkBundle\Component\Sitemap\SitemapItem[]
     */
    public function getSitemapItemsForArticlesOnDomain(DomainConfig $domainConfig)
    {
        $queryBuilder = $this->articleRepository->getVisibleArticlesByDomainIdQueryBuilder($domainConfig->getId());
        $queryBuilder
            ->select('fu.slug')
            ->join(
                FriendlyUrl::class,
                'fu',
                Join::WITH,
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
     * @return \Shopsys\FrameworkBundle\Component\Sitemap\SitemapItem[]
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
