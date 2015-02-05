<?php

namespace SS6\ShopBundle\Model\Category;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use SS6\ShopBundle\Model\Category\Category;

class CategoryRepository {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	private function getCategoryRepository() {
		return $this->em->getRepository(Category::class);
	}

	/**
	 * @return \SS6\ShopBundle\Model\Category\Category[]
	 */
	public function getAll() {
		return $this->getCategoryRepository()->findBy([], ['root' => 'ASC', 'lft' => 'ASC']);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Category\Category $categoryBranch
	 * @return \SS6\ShopBundle\Model\Category\Category[]
	 */
	public function getAllWithoutBranch(Category $categoryBranch) {
		return $this->em->createQueryBuilder()
			->select('c')
			->from(Category::class, 'c')
			->where('c.root != :branchRoot OR c.lft < :branchLft OR c.rgt > :branchRgt')
			->orderBy('c.root, c.lft', 'ASC')
			->setParameter('branchRoot', $categoryBranch->getRoot())
			->setParameter('branchLft', $categoryBranch->getLft())
			->setParameter('branchRgt', $categoryBranch->getRgt())
			->getQuery()
			->execute();
	}

	/**
	 * @param int $categoryId
	 * @return \SS6\ShopBundle\Model\Category\Category|null
	 */
	public function findById($categoryId) {
		return $this->getCategoryRepository()->find($categoryId);
	}

	/**
	 * @param int $categoryId
	 * @return \SS6\ShopBundle\Model\Category\Category
	 */
	public function getById($categoryId) {
		$category = $this->findById($categoryId);

		if ($category === null) {
			throw new \SS6\ShopBundle\Model\Category\Exception\CategoryNotFoundException($categoryId);
		}

		return $category;
	}

	/**
	 * @param string $locale
	 * @return \SS6\ShopBundle\Model\Category\Category[]
	 */
	public function getAllInRootWithTranslation($locale) {
		return $this->getAllWithTranslationQueryBuilder($locale)
			->andWhere('c.level = 0')
			->getQuery()
			->execute();
	}

	/**
	 * @return \SS6\ShopBundle\Model\Category\Category[]
	 */
	public function getAllInRootEagerLoaded() {
		$allCategories = $this->em->createQueryBuilder()
			->select('c')
			->from(Category::class, 'c')
			->join('c.translations', 'ct')
			->orderBy('c.root, c.lft', 'ASC')
			->getQuery()
			->execute();

		$rootCategories = [];
		foreach ($allCategories as $cateogry) {
			if ($cateogry->getLevel() === 0) {
				$rootCategories[] = $cateogry;
			}
		}

		return $rootCategories;
	}

	/**
	 * @param string $locale
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	private function getAllWithTranslationQueryBuilder($locale) {
		$qb = $this->em->createQueryBuilder()
			->select('c')
			->from(Category::class, 'c')
			->join('c.translations', 'ct', Join::WITH, 'ct.locale = :locale')
			->where('ct.name IS NOT NULL')
			->orderBy('c.root, c.lft', 'ASC');
		$qb->setParameter('locale', $locale);

		return $qb;
	}

}
