<?php

namespace Shopsys\ShopBundle\Model\Category\TopCategory;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Model\Category\Category;
use Shopsys\ShopBundle\Model\Category\TopCategory\TopCategoryRepository;

class TopCategoryFacade
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Model\Category\TopCategory\TopCategoryRepository
     */
    private $topCategoryRepository;

    public function __construct(
        EntityManager $em,
        TopCategoryRepository $topCategoryRepository
    ) {
        $this->em = $em;
        $this->topCategoryRepository = $topCategoryRepository;
    }

    /**
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Category\TopCategory\TopCategory[]
     */
    public function getAll($domainId)
    {
        return $this->topCategoryRepository->getAll($domainId);
    }

    /**
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Category\Category[]
     */
    public function getCategoriesForAll($domainId)
    {
        $topCategories = $this->getAll($domainId);
        $categories = [];

        foreach ($topCategories as $topCategory) {
            $categories[] = $topCategory->getCategory();
        }

        return $categories;
    }

    /**
     * @param $domainId
     * @param \Shopsys\ShopBundle\Model\Category\Category[] $categories
     */
    public function saveTopCategoriesForDomain($domainId, array $categories)
    {
        $oldTopCategories = $this->topCategoryRepository->getAll($domainId);
        foreach ($oldTopCategories as $oldTopCategory) {
            $this->em->remove($oldTopCategory);
        }
        $this->em->flush($oldTopCategories);

        $topCategories = [];
        $position = 1;
        foreach ($categories as $category) {
            $topCategory = new TopCategory($category, $domainId, $position++);
            $this->em->persist($topCategory);
            $topCategories[] = $topCategory;
        }
        $this->em->flush($topCategories);
    }
}
