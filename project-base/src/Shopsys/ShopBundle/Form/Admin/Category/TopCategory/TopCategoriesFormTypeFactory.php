<?php

namespace Shopsys\ShopBundle\Form\Admin\Category\TopCategory;

use Shopsys\ShopBundle\Component\Transformers\CategoriesIdsToCategoriesTransformer;
use Shopsys\ShopBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer;
use Shopsys\ShopBundle\Form\Admin\Category\TopCategory\TopCategoriesFormType;
use Shopsys\ShopBundle\Model\Category\CategoryFacade;

class TopCategoriesFormTypeFactory
{

    /**
     * @var \Shopsys\ShopBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer
     */
    private $removeDuplicatesTransformer;

    /**
     * @var \Shopsys\ShopBundle\Model\Category\CategoryFacade
     */
    private $categoryFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Transformers\CategoriesIdsToCategoriesTransformer
     */
    private $categoriesIdsToCategoriesTransformer;

    /**
     * @param \Shopsys\ShopBundle\Model\Category\CategoryFacade $categoryFacade
     * @param \Shopsys\ShopBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer $removeDuplicatesTransformer
     * @param \Shopsys\ShopBundle\Component\Transformers\CategoriesIdsToCategoriesTransformer $categoriesIdsToCategoriesTransformer
     */
    public function __construct(
        CategoryFacade $categoryFacade,
        RemoveDuplicatesFromArrayTransformer $removeDuplicatesTransformer,
        CategoriesIdsToCategoriesTransformer $categoriesIdsToCategoriesTransformer
    ) {
        $this->categoryFacade = $categoryFacade;
        $this->removeDuplicatesTransformer = $removeDuplicatesTransformer;
        $this->categoriesIdsToCategoriesTransformer = $categoriesIdsToCategoriesTransformer;
    }

    /**
     * @param int $domainId
     * @param string $locale
     * @return \Shopsys\ShopBundle\Form\Admin\Category\TopCategory\TopCategoriesFormType
     */
    public function create($domainId, $locale) {
        $categoryPaths = $this->categoryFacade->getFullPathsIndexedByIdsForDomain($domainId, $locale);

        return new TopCategoriesFormType(
            $categoryPaths,
            $this->removeDuplicatesTransformer,
            $this->categoriesIdsToCategoriesTransformer
        );
    }

}
