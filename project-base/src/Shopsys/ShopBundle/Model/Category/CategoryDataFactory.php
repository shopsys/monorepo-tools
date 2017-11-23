<?php

namespace Shopsys\ShopBundle\Model\Category;

use Shopsys\ShopBundle\Component\Plugin\PluginCrudExtensionFacade;
use Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade;

class CategoryDataFactory
{
    /**
     * @var \Shopsys\ShopBundle\Model\Category\CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
     */
    private $friendlyUrlFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Plugin\PluginCrudExtensionFacade
     */
    private $pluginCrudExtensionFacade;

    public function __construct(
        CategoryRepository $categoryRepository,
        FriendlyUrlFacade $friendlyUrlFacade,
        PluginCrudExtensionFacade $pluginCrudExtensionFacade
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->friendlyUrlFacade = $friendlyUrlFacade;
        $this->pluginCrudExtensionFacade = $pluginCrudExtensionFacade;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Category\Category $category
     * @return \Shopsys\ShopBundle\Model\Category\CategoryData
     */
    public function createFromCategory(Category $category)
    {
        $categoryDomains = $this->categoryRepository->getCategoryDomainsByCategory($category);

        $categoryData = new CategoryData();
        $categoryData->setFromEntity($category, $categoryDomains);

        foreach ($categoryDomains as $categoryDomain) {
            $domainId = $categoryDomain->getDomainId();

            $categoryData->urls->mainFriendlyUrlsByDomainId[$domainId] =
                $this->friendlyUrlFacade->findMainFriendlyUrl($domainId, 'front_product_list', $category->getId());
        }

        $categoryData->pluginData = $this->pluginCrudExtensionFacade->getAllData('category', $category->getId());

        return $categoryData;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Category\CategoryData
     */
    public function createDefault()
    {
        return new CategoryData();
    }
}
