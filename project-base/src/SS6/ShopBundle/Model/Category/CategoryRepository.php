<?php

namespace SS6\ShopBundle\Model\Category;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use SS6\ShopBundle\Model\Category\Category;

class CategoryRepository extends NestedTreeRepository {

	const MOVE_DOWN_TO_BOTTOM = true;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(EntityManager $em) {
		$this->em = $em;
		$classMetadata = $this->em->getClassMetadata(Category::class);
		parent::__construct($this->em, $classMetadata);
	}

	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	private function getCategoryRepository() {
		return $this->em->getRepository(Category::class);
	}

	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	private function getCategoryDomainRepository() {
		return $this->em->getRepository(CategoryDomain::class);
	}

	/**
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	private function getAllQueryBuilder() {
		return $this->getCategoryRepository()
			->createQueryBuilder('c')
			->where('c.parent IS NOT NULL')
			->orderBy('c.lft');
	}

	/**
	 * @return \SS6\ShopBundle\Model\Category\Category[]
	 */
	public function getAll() {
		return $this->getAllQueryBuilder()
			->getQuery()
			->getResult();
	}

	/**
	 * @return \SS6\ShopBundle\Model\Category\Category
	 */
	public function getRootCategory() {
		return $this->getCategoryRepository()->findOneBy(['parent' => null]);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Category\Category $categoryBranch
	 * @return \SS6\ShopBundle\Model\Category\Category[]
	 */
	public function getAllWithoutBranch(Category $categoryBranch) {
		return $this->getAllQueryBuilder()
			->andWhere('c.lft < :branchLft OR c.rgt > :branchRgt')
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
			$message = 'Category with ID ' . $categoryId . ' not found.';
			throw new \SS6\ShopBundle\Model\Category\Exception\CategoryNotFoundException($message);
		}

		return $category;
	}

	/**
	 * @param int $domainId
	 * @param string $locale
	 * @return \SS6\ShopBundle\Model\Category\Category[]
	 */
	public function getPreOrderTreeTraversalForVisibleCategoriesByDomain($domainId, $locale) {
		$queryBuilder = $this->getAllQueryBuilder();
		$this->addTranslation($queryBuilder, $locale);

		$queryBuilder
			->join(CategoryDomain::class, 'cd', Join::WITH, 'cd.category = c')
			->andWhere('c.level >= 1')
			->andWhere('cd.domainId = :domainId')
			->andWhere('cd.visible = TRUE')
			->orderBy('c.lft');

		$queryBuilder->setParameter('domainId', $domainId);

		return $queryBuilder->getQuery()->execute();
	}

	/**
	 * @return \SS6\ShopBundle\Model\Category\Category[]
	 */
	public function getAllInRootEagerLoaded() {
		$allCategories = $this->getAllQueryBuilder()
			->join('c.translations', 'ct')
			->getQuery()
			->execute();

		$rootCategories = [];
		foreach ($allCategories as $cateogry) {
			if ($cateogry->getLevel() === 1) {
				$rootCategories[] = $cateogry;
			}
		}

		return $rootCategories;
	}

	/**
	 * @param string $locale
	 */
	private function addTranslation(QueryBuilder $categoriesQueryBuilder, $locale) {
		$categoriesQueryBuilder
			->join('c.translations', 'ct', Join::WITH, 'ct.locale = :locale')
			->setParameter('locale', $locale);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Category\Category $category
	 * @return \SS6\ShopBundle\Model\Category\CategoryDomain[]
	 */
	public function getCategoryDomainsByCategory(Category $category) {
		return $this->getCategoryDomainRepository()->findBy([
			'category' => $category,
		]);
	}

}
