<?php

namespace Shopsys\FrameworkBundle\Model\Category;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Plugin\PluginCrudExtensionFacade;
use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade;

class CategoryDataFactory implements CategoryDataFactoryInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
     */
    protected $friendlyUrlFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Plugin\PluginCrudExtensionFacade
     */
    protected $pluginCrudExtensionFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    public function __construct(
        CategoryRepository $categoryRepository,
        FriendlyUrlFacade $friendlyUrlFacade,
        PluginCrudExtensionFacade $pluginCrudExtensionFacade,
        Domain $domain
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->friendlyUrlFacade = $friendlyUrlFacade;
        $this->pluginCrudExtensionFacade = $pluginCrudExtensionFacade;
        $this->domain = $domain;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $category
     * @return \Shopsys\FrameworkBundle\Model\Category\CategoryData
     */
    public function createFromCategory(Category $category): CategoryData
    {
        $categoryData = new CategoryData();
        $this->fillFromCategory($categoryData, $category);

        return $categoryData;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Category\CategoryData
     */
    public function create(): CategoryData
    {
        $categoryData = new CategoryData();
        $this->fillNew($categoryData);

        return $categoryData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\CategoryData $categoryData
     */
    protected function fillNew(CategoryData $categoryData)
    {
        foreach ($this->domain->getAllIds() as $domainId) {
            $categoryData->seoMetaDescriptions[$domainId] = null;
            $categoryData->seoTitles[$domainId] = null;
            $categoryData->seoH1s[$domainId] = null;
            $categoryData->descriptions[$domainId] = null;
            $categoryData->enabled[$domainId] = true;
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\CategoryData $categoryData
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $category
     */
    protected function fillFromCategory(CategoryData $categoryData, Category $category)
    {
        $categoryData->name = $category->getNames();
        $categoryData->parent = $category->getParent();

        foreach ($this->domain->getAllIds() as $domainId) {
            $categoryData->seoMetaDescriptions[$domainId] = $category->getSeoMetaDescription($domainId);
            $categoryData->seoTitles[$domainId] = $category->getSeoTitle($domainId);
            $categoryData->seoH1s[$domainId] = $category->getSeoH1($domainId);
            $categoryData->descriptions[$domainId] = $category->getDescription($domainId);
            $categoryData->enabled[$domainId] = $category->isEnabled($domainId);

            $mainFriendlyUrl = $this->friendlyUrlFacade->findMainFriendlyUrl($domainId, 'front_product_list', $category->getId());
            $categoryData->urls->mainFriendlyUrlsByDomainId[$domainId] = $mainFriendlyUrl;
        }

        $categoryData->pluginData = $this->pluginCrudExtensionFacade->getAllData('category', $category->getId());
    }
}
