<?php

namespace Shopsys\ShopBundle\Component\Transformers;

use IteratorAggregate;
use Shopsys\ShopBundle\Model\Category\CategoryRepository;
use Symfony\Component\Form\DataTransformerInterface;

class CategoriesIdsToCategoriesTransformer implements DataTransformerInterface
{
    /**
     * @var \Shopsys\ShopBundle\Model\Category\CategoryRepository
     */
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Category\Category[]|null $categories
     * @return int[]
     */
    public function transform($categories)
    {
        $categoriesIds = [];

        if (is_array($categories) || $categories instanceof IteratorAggregate) {
            foreach ($categories as $category) {
                $categoriesIds[] = $category->getId();
            }
        }

        return $categoriesIds;
    }

    /**
     * @param int[] $categoriesIds
     * @return \Shopsys\ShopBundle\Model\Category\Category[]|null
     */
    public function reverseTransform($categoriesIds)
    {
        $categories = [];

        if (is_array($categoriesIds)) {
            foreach ($categoriesIds as $categoryId) {
                try {
                    $categories[] = $this->categoryRepository->getById($categoryId);
                } catch (\Shopsys\ShopBundle\Model\Category\Exception\CategoryNotFoundException $e) {
                    throw new \Symfony\Component\Form\Exception\TransformationFailedException('Category not found', null, $e);
                }
            }
        }

        return $categories;
    }
}
