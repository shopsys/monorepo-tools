<?php

namespace Shopsys\FrameworkBundle\Model\Category;

use Shopsys\FrameworkBundle\Component\Plugin\PluginCrudExtensionFacade;
use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade;

class CategoryDataFactory
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
     */
    private $friendlyUrlFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Plugin\PluginCrudExtensionFacade
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
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $category
     * @return \Shopsys\FrameworkBundle\Model\Category\CategoryData
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
     * @return \Shopsys\FrameworkBundle\Model\Category\CategoryData
     */
    public function createDefault()
    {
        return new CategoryData();
    }
}
