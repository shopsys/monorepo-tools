<?php

namespace Shopsys\FrameworkBundle\Component\Transformers;

use Shopsys\FrameworkBundle\Model\Category\CategoryFacade;
use Symfony\Component\Form\DataTransformerInterface;

class CategoriesTypeTransformer implements DataTransformerInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\CategoryFacade
     */
    private $categoryFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\CategoryFacade $categoryFacade
     */
    public function __construct(CategoryFacade $categoryFacade)
    {
        $this->categoryFacade = $categoryFacade;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category[]|null $categories
     * @return bool[]
     */
    public function transform($categories)
    {
        $categories = $categories ?? [];
        $allCategories = $this->categoryFacade->getAllCategoriesOfCollapsedTree($categories);

        $isCheckedIndexedByCategoryId = [];
        foreach ($allCategories as $category) {
            $isChecked = in_array($category, $categories, true);
            $isCheckedIndexedByCategoryId[$category->getId()] = $isChecked;
        }

        return $isCheckedIndexedByCategoryId;
    }

    /**
     * @param bool[]|null $isCheckedIndexedByCategoryId
     * @return \Shopsys\FrameworkBundle\Model\Category\Category[]
     */
    public function reverseTransform($isCheckedIndexedByCategoryId)
    {
        $categories = [];
        foreach ($isCheckedIndexedByCategoryId ?? [] as $categoryId => $isChecked) {
            if ($isChecked) {
                $categories[] = $this->categoryFacade->getById($categoryId);
            }
        }

        return $categories;
    }
}
