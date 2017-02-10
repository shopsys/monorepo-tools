<?php

namespace Shopsys\ShopBundle\Model\Category\TopCategory;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Model\Category\Category;
use Shopsys\ShopBundle\Model\Category\CategoryRepository;
use Shopsys\ShopBundle\Model\Category\TopCategory\TopCategory;

class TopCategoryRepository {

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Model\Category\CategoryRepository
     */
    private $categoryRepository;

    public function __construct(EntityManager $entityManager, CategoryRepository $categoryRepository) {
        $this->em = $entityManager;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getTopCategoryRepository() {
        return $this->em->getRepository(TopCategory::class);
    }

    /**
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Category\TopCategory\TopCategory[]
     */
    public function getAll($domainId) {
        return $this->getTopCategoryRepository()->findBy(['domainId' => $domainId], ['position' => 'ASC']);
    }

}
