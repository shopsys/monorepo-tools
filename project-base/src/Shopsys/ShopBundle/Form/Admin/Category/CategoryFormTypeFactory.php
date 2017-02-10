<?php

namespace Shopsys\ShopBundle\Form\Admin\Category;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Form\Admin\Category\CategoryFormType;
use Shopsys\ShopBundle\Model\Category\Category;
use Shopsys\ShopBundle\Model\Category\CategoryRepository;
use Shopsys\ShopBundle\Model\Feed\Category\FeedCategoryRepository;
use Shopsys\ShopBundle\Model\Seo\SeoSettingFacade;

class CategoryFormTypeFactory
{

    /**
     * @var \Shopsys\ShopBundle\Model\Category\CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Feed\Category\FeedCategoryRepository
     */
    private $feedCategoryRepository;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\ShopBundle\Model\Seo\SeoSettingFacade
     */
    private $seoSettingFacade;

    public function __construct(
        CategoryRepository $categoryRepository,
        FeedCategoryRepository $feedCategoryRepository,
        Domain $domain,
        SeoSettingFacade $seoSettingFacade
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->feedCategoryRepository = $feedCategoryRepository;
        $this->domain = $domain;
        $this->seoSettingFacade = $seoSettingFacade;
    }

    /**
     * @return \Shopsys\ShopBundle\Form\Admin\Category\CategoryFormType
     */
    public function create() {
        $categories = $this->categoryRepository->getAll();
        $heurekaCzFeedCategories = $this->feedCategoryRepository->getAllHeurekaCz();
        $domains = $this->domain->getAll();
        $metaDescriptionsIndexedByDomainId = $this->seoSettingFacade->getDescriptionsMainPageIndexedByDomainIds($domains);

        return new CategoryFormType(
            $categories,
            $heurekaCzFeedCategories,
            $domains,
            $metaDescriptionsIndexedByDomainId
        );
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Category\Category $category
     * @return \Shopsys\ShopBundle\Form\Admin\Category\CategoryFormType
     */
    public function createForCategory(Category $category) {
        $categories = $this->categoryRepository->getAllWithoutBranch($category);
        $heurekaCzFeedCategories = $this->feedCategoryRepository->getAllHeurekaCz();
        $domains = $this->domain->getAll();
        $metaDescriptionsIndexedByDomainId = $this->seoSettingFacade->getDescriptionsMainPageIndexedByDomainIds($domains);

        return new CategoryFormType(
            $categories,
            $heurekaCzFeedCategories,
            $domains,
            $metaDescriptionsIndexedByDomainId,
            $category
        );
    }
}
