<?php

namespace Shopsys\ShopBundle\Model\Feed;

use Shopsys\ProductFeed\DomainConfigInterface;
use Shopsys\ProductFeed\FeedItemInterface;
use Shopsys\ProductFeed\HeurekaCategoryNameProviderInterface;
use Shopsys\ShopBundle\Model\Category\CategoryRepository;
use Shopsys\ShopBundle\Model\Product\ProductRepository;

class HeurekaCategoryNameProvider implements HeurekaCategoryNameProviderInterface
{
    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductRepository
     */
    private $productRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Category\CategoryRepository
     */
    private $categoryRepository;

    public function __construct(ProductRepository $productRepository, CategoryRepository $categoryRepository)
    {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param \Shopsys\ProductFeed\FeedItemInterface $item
     * @param \Shopsys\ProductFeed\DomainConfigInterface $domainConfig
     * @return string|null
     */
    public function getHeurekaCategoryNameForItem(FeedItemInterface $item, DomainConfigInterface $domainConfig)
    {
        $product = $this->productRepository->getById($item->getId());
        $category = $this->categoryRepository->findProductMainCategoryOnDomain($product, $domainConfig->getId());
        $feedCategory = $category !== null ? $category->getHeurekaCzFeedCategory() : null;

        return $feedCategory !== null ? $feedCategory->getFullName() : null;
    }
}
