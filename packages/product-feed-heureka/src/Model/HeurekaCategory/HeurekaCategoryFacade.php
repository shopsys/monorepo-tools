<?php

namespace Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Model\Category\CategoryRepository;

class HeurekaCategoryFacade
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryRepository
     */
    private $heurekaCategoryRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\CategoryRepository
     */
    private $categoryRepository;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryRepository $heurekaCategoryRepository
     * @param \Shopsys\FrameworkBundle\Model\Category\CategoryRepository $categoryRepository
     */
    public function __construct(
        EntityManagerInterface $em,
        HeurekaCategoryRepository $heurekaCategoryRepository,
        CategoryRepository $categoryRepository
    ) {
        $this->em = $em;
        $this->heurekaCategoryRepository = $heurekaCategoryRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryData[] $newHeurekaCategoriesData
     */
    public function saveHeurekaCategories(array $newHeurekaCategoriesData)
    {
        $existingHeurekaCategories = $this->heurekaCategoryRepository->getAllIndexedById();

        $this->removeOldHeurekaCategories($newHeurekaCategoriesData, $existingHeurekaCategories);

        foreach ($newHeurekaCategoriesData as $newHeurekaCategoryData) {
            if (!array_key_exists($newHeurekaCategoryData->id, $existingHeurekaCategories)) {
                $newHeurekaCategory = new HeurekaCategory($newHeurekaCategoryData);
                $this->em->persist($newHeurekaCategory);
            } else {
                $existingHeurekaCategory = $existingHeurekaCategories[$newHeurekaCategoryData->id];
                $existingHeurekaCategory->edit($newHeurekaCategoryData);
            }
        }

        $this->em->flush();
    }

    /**
     * @param \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryData[] $newHeurekaCategoriesData
     * @param \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategory[] $existingHeurekaCategoriesIndexedByIds
     */
    private function removeOldHeurekaCategories(
        array $newHeurekaCategoriesData,
        array $existingHeurekaCategoriesIndexedByIds
    ) {
        $existingHeurekaCategoriesIds = array_keys($existingHeurekaCategoriesIndexedByIds);

        $newHeurekaCategoriesIds = [];
        foreach ($newHeurekaCategoriesData as $newHeurekaCategoryData) {
            $newHeurekaCategoriesIds[] = $newHeurekaCategoryData->id;
        }

        $categoryIdsToDelete = array_diff($existingHeurekaCategoriesIds, $newHeurekaCategoriesIds);

        foreach ($categoryIdsToDelete as $categoryIdToDelete) {
            $this->em->remove($existingHeurekaCategoriesIndexedByIds[$categoryIdToDelete]);
        }
    }

    /**
     * @param int $categoryId
     * @param \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategory $heurekaCategory
     */
    public function changeHeurekaCategoryForCategoryId($categoryId, HeurekaCategory $heurekaCategory)
    {
        $oldHeurekaCategoryByCategoryId = $this->heurekaCategoryRepository->findByCategoryId($categoryId);

        $category = $this->categoryRepository->getById($categoryId);

        if ($oldHeurekaCategoryByCategoryId === null) {
            $heurekaCategory->addCategory($category);
        } elseif ($oldHeurekaCategoryByCategoryId->getId() !== $heurekaCategory->getId()) {
            $oldHeurekaCategoryByCategoryId->removeCategory($category);
            $heurekaCategory->addCategory($category);
        }

        $this->em->flush();
    }

    /**
     * @param int $categoryId
     * @return \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategory|null
     */
    public function findByCategoryId($categoryId)
    {
        return $this->heurekaCategoryRepository->findByCategoryId($categoryId);
    }

    /**
     * @param int $categoryId
     */
    public function removeHeurekaCategoryForCategoryId($categoryId)
    {
        $heurekaCategory = $this->heurekaCategoryRepository->findByCategoryId($categoryId);

        if ($heurekaCategory !== null) {
            $category = $this->categoryRepository->getById($categoryId);
            $heurekaCategory->removeCategory($category);

            $this->em->flush();
        }
    }

    /**
     * @param int $id
     * @return \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategory
     */
    public function getOneById($id)
    {
        return $this->heurekaCategoryRepository->getOneById($id);
    }

    /**
     * @return \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategory[]
     */
    public function getAllIndexedById()
    {
        return $this->heurekaCategoryRepository->getAllIndexedById();
    }
}
