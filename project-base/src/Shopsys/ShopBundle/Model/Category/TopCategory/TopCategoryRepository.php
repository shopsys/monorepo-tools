<?php

namespace SS6\ShopBundle\Model\Category\TopCategory;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Category\Category;
use SS6\ShopBundle\Model\Category\CategoryRepository;
use SS6\ShopBundle\Model\Category\TopCategory\TopCategory;

class TopCategoryRepository {

	/**
	 * @var \Doctrine\ORM\EntityRepository
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Category\CategoryRepository
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
	 * @return \SS6\ShopBundle\Model\Category\TopCategory\TopCategory[]
	 */
	public function getAll($domainId) {
		return $this->getTopCategoryRepository()->findBy(['domainId' => $domainId], ['position' => 'ASC']);
	}

}
