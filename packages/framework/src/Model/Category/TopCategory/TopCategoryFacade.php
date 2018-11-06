<?php

namespace Shopsys\FrameworkBundle\Model\Category\TopCategory;

use Doctrine\ORM\EntityManagerInterface;

class TopCategoryFacade
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\TopCategory\TopCategoryRepository
     */
    protected $topCategoryRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\TopCategory\TopCategoryFactoryInterface
     */
    protected $topCategoryFactory;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Category\TopCategory\TopCategoryRepository $topCategoryRepository
     * @param \Shopsys\FrameworkBundle\Model\Category\TopCategory\TopCategoryFactoryInterface $topCategoryFactory
     */
    public function __construct(
        EntityManagerInterface $em,
        TopCategoryRepository $topCategoryRepository,
        TopCategoryFactoryInterface $topCategoryFactory
    ) {
        $this->em = $em;
        $this->topCategoryRepository = $topCategoryRepository;
        $this->topCategoryFactory = $topCategoryFactory;
    }

    /**
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Category\Category[]
     */
    public function getAllCategoriesByDomainId($domainId)
    {
        $topCategories = $this->topCategoryRepository->getAllByDomainId($domainId);

        return $this->getCategoriesFromTopCategories($topCategories);
    }

    /**
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Category\Category[]
     */
    public function getVisibleCategoriesByDomainId($domainId)
    {
        $topCategories = $this->topCategoryRepository->getVisibleByDomainId($domainId);

        return $this->getCategoriesFromTopCategories($topCategories);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\TopCategory\TopCategory[] $topCategories
     * @return \Shopsys\FrameworkBundle\Model\Category\Category[]
     */
    protected function getCategoriesFromTopCategories($topCategories)
    {
        $categories = [];

        foreach ($topCategories as $topCategory) {
            $categories[] = $topCategory->getCategory();
        }

        return $categories;
    }

    /**
     * @param int $domainId
     * @param \Shopsys\FrameworkBundle\Model\Category\Category[] $categories
     */
    public function saveTopCategoriesForDomain($domainId, array $categories)
    {
        $oldTopCategories = $this->topCategoryRepository->getAllByDomainId($domainId);
        foreach ($oldTopCategories as $oldTopCategory) {
            $this->em->remove($oldTopCategory);
        }
        $this->em->flush($oldTopCategories);

        $topCategories = [];
        $position = 1;
        foreach ($categories as $category) {
            $topCategory = $this->topCategoryFactory->create($category, $domainId, $position++);
            $this->em->persist($topCategory);
            $topCategories[] = $topCategory;
        }
        $this->em->flush($topCategories);
    }
}
