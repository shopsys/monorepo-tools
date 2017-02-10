<?php

namespace Shopsys\ShopBundle\Model\Category;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ShopBundle\Component\Paginator\QueryPaginator;
use Shopsys\ShopBundle\Component\String\DatabaseSearching;
use Shopsys\ShopBundle\Model\Category\Category;
use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Product\ProductCategoryDomain;
use Shopsys\ShopBundle\Model\Product\ProductRepository;

class CategoryRepository extends NestedTreeRepository {

    const MOVE_DOWN_TO_BOTTOM = true;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductRepository
     */
    private $productRepository;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Shopsys\ShopBundle\Model\Product\ProductRepository
     */
    public function __construct(EntityManager $em, ProductRepository $productRepository) {
        $this->em = $em;
        $classMetadata = $this->em->getClassMetadata(Category::class);
        $this->productRepository = $productRepository;
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
     * @return \Shopsys\ShopBundle\Model\Category\Category[]
     */
    public function getAll() {
        return $this->getAllQueryBuilder()
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $domainId
     * @param string $locale
     * @return \Shopsys\ShopBundle\Model\Category\Category[]
     */
    public function getFullPathsIndexedByIdsForDomain($domainId, $locale) {
        $queryBuilder = $this->getPreOrderTreeTraversalForAllCategoriesByDomainQueryBuilder($domainId, $locale);

        $rows = $queryBuilder->select('c.id, IDENTITY(c.parent) AS parentId, ct.name')->getQuery()->getScalarResult();

        $fullPathsById = [];
        foreach ($rows as $row) {
            if (array_key_exists($row['parentId'], $fullPathsById)) {
                $fullPathsById[$row['id']] = $fullPathsById[$row['parentId']] . ' - ' . $row['name'];
            } else {
                $fullPathsById[$row['id']] = $row['name'];
            }
        }

        return $fullPathsById;
    }

    /**
     * @return int[]
     */
    public function getAllIds() {
        $result = $this->getAllQueryBuilder()
            ->select('c.id')
            ->getQuery()
            ->getScalarResult();

        return array_map('current', $result);
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Category\Category
     */
    public function getRootCategory() {
        $rootCategory = $this->getCategoryRepository()->findOneBy(['parent' => null]);

        if ($rootCategory === null) {
            $message = 'Root category not found';
            throw new \Shopsys\ShopBundle\Model\Category\Exception\RootCategoryNotFoundException($message);
        }

        return $rootCategory;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Category\Category $categoryBranch
     * @return \Shopsys\ShopBundle\Model\Category\Category[]
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
     * @return \Shopsys\ShopBundle\Model\Category\Category|null
     */
    public function findById($categoryId) {
        return $this->getAllQueryBuilder()
            ->andWhere('c.id = :categoryId')
            ->setParameter('categoryId', $categoryId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param int $categoryId
     * @return \Shopsys\ShopBundle\Model\Category\Category
     */
    public function getById($categoryId) {
        $category = $this->findById($categoryId);

        if ($category === null) {
            $message = 'Category with ID ' . $categoryId . ' not found.';
            throw new \Shopsys\ShopBundle\Model\Category\Exception\CategoryNotFoundException($message);
        }

        return $category;
    }

    /**
     * @param string $locale
     * @return \Shopsys\ShopBundle\Model\Category\Category[]
     */
    public function getPreOrderTreeTraversalForAllCategories($locale) {
        $queryBuilder = $this->getAllQueryBuilder();
        $this->addTranslation($queryBuilder, $locale);

        $queryBuilder
            ->andWhere('c.level >= 1')
            ->orderBy('c.lft');

        return $queryBuilder->getQuery()->execute();
    }

    /**
     * @param int $domainId
     * @param string $locale
     * @return \Shopsys\ShopBundle\Model\Category\Category[]
     */
    public function getPreOrderTreeTraversalForVisibleCategoriesByDomain($domainId, $locale) {
        $queryBuilder = $this->getPreOrderTreeTraversalForAllCategoriesByDomainQueryBuilder($domainId, $locale);

        $queryBuilder->andWhere('cd.visible = TRUE');

        return $queryBuilder->getQuery()->execute();
    }

    /**
     * @param int $domainId
     * @param string $locale
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getPreOrderTreeTraversalForAllCategoriesByDomainQueryBuilder($domainId, $locale) {
        $queryBuilder = $this->getAllQueryBuilder();
        $this->addTranslation($queryBuilder, $locale);

        $queryBuilder
            ->join(CategoryDomain::class, 'cd', Join::WITH, 'cd.category = c')
            ->andWhere('c.level >= 1')
            ->andWhere('cd.domainId = :domainId')
            ->setParameter('domainId', $domainId)
            ->orderBy('c.lft');

        return $queryBuilder;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Category\Category $parentCategory
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return \Shopsys\ShopBundle\Model\Category\Category[]
     */
    public function getTranslatedVisibleSubcategoriesByDomain(Category $parentCategory, DomainConfig $domainConfig) {
        $queryBuilder = $this->getAllVisibleByDomainIdQueryBuilder($domainConfig->getId());
        $this->addTranslation($queryBuilder, $domainConfig->getLocale());

        $queryBuilder
            ->andWhere('c.parent = :parentCategory')
            ->setParameter('parentCategory', $parentCategory);

        return $queryBuilder->getQuery()->execute();
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $categoriesQueryBuilder
     * @param string $locale
     */
    private function addTranslation(QueryBuilder $categoriesQueryBuilder, $locale) {
        $categoriesQueryBuilder
            ->addSelect('ct')
            ->join('c.translations', 'ct', Join::WITH, 'ct.locale = :locale')
            ->setParameter('locale', $locale);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Category\Category $category
     * @return \Shopsys\ShopBundle\Model\Category\CategoryDomain[]
     */
    public function getCategoryDomainsByCategory(Category $category) {
        return $this->getCategoryDomainRepository()->findBy([
            'category' => $category,
        ]);
    }

    /**
     * @param string|null $searchText
     * @param int $domainId
     * @param string $locale
     * @param int $page
     * @param int $limit
     * @return \Shopsys\ShopBundle\Component\Paginator\PaginationResult
     */
    public function getPaginationResultForSearchVisible(
        $searchText,
        $domainId,
        $locale,
        $page,
        $limit
    ) {
        $queryBuilder = $this->getVisibleByDomainIdAndSearchTextQueryBuilder($domainId, $locale, $searchText);
        $queryBuilder->orderBy('ct.name');

        $queryPaginator = new QueryPaginator($queryBuilder);

        return $queryPaginator->getResult($page, $limit);
    }

    /**
     * @param int $domainId
     * @param string $locale
     * @param string|null $searchText
     * @return \Shopsys\ShopBundle\Model\Category\Category[]
     */
    public function getVisibleByDomainIdAndSearchText($domainId, $locale, $searchText) {
        $queryBuilder = $this->getVisibleByDomainIdAndSearchTextQueryBuilder(
            $domainId,
            $locale,
            $searchText
        );

        return $queryBuilder->getQuery()->execute();
    }

    /**
     * @param int $domainId
     * @param string $locale
     * @param string|null $searchText
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getVisibleByDomainIdAndSearchTextQueryBuilder(
        $domainId,
        $locale,
        $searchText
    ) {
        $queryBuilder = $this->getAllVisibleByDomainIdQueryBuilder($domainId);
        $this->addTranslation($queryBuilder, $locale);
        $this->filterBySearchText($queryBuilder, $searchText);

        return $queryBuilder;
    }

    /**
     * @param int $domainId
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getAllVisibleByDomainIdQueryBuilder($domainId) {
        $queryBuilder = $this->getAllQueryBuilder()
            ->join(CategoryDomain::class, 'cd', Join::WITH, 'cd.category = c.id')
            ->andWhere('cd.domainId = :domainId')
            ->andWhere('cd.visible = TRUE');

        $queryBuilder->setParameter('domainId', $domainId);

        return $queryBuilder;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Category\Category $category
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Category\Category[]
     */
    public function getAllVisibleChildrenByCategoryAndDomainId(Category $category, $domainId) {
        $queryBuilder = $this->getAllVisibleByDomainIdQueryBuilder($domainId)
            ->andWhere('c.parent = :category')
            ->setParameter('category', $category);

        return $queryBuilder->getQuery()->execute();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Category\Category $category
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @param int $domainId
     */
    public function getListableProductsCountByCategory($category, $pricingGroup, $domainId) {
        $queryBuilder = $this->productRepository->getAllListableQueryBuilder($domainId, $pricingGroup);

        $queryBuilder->join(ProductCategoryDomain::class, 'pcd', Join::WITH,
            'pcd.product = p
             AND pcd.category = :category
             AND pcd.domainId = :domainId'
        )
        ->select('COUNT(p)')
        ->resetDQLPart('orderBy');

        $queryBuilder->setParameter('category', $category);
        $queryBuilder->setParameter('domainId', $domainId);

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param string|null $searchText
     */
    private function filterBySearchText(QueryBuilder $queryBuilder, $searchText) {
        $queryBuilder->andWhere(
            'NORMALIZE(ct.name) LIKE NORMALIZE(:searchText)'
        );
        $queryBuilder->setParameter('searchText', '%' . DatabaseSearching::getLikeSearchString($searchText) . '%');
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Category\Category|null
     */
    public function findProductMainCategoryOnDomain(Product $product, $domainId) {
        $qb = $this->getAllVisibleByDomainIdQueryBuilder($domainId)
            ->join(ProductCategoryDomain::class, 'pcd', Join::WITH,
                'pcd.product = :product
                    AND pcd.category = c
                    AND pcd.domainId = :domainId'
            )
            ->orderBy('c.level DESC, c.lft')
            ->setMaxResults(1);

        $qb->setParameters([
            'domainId' => $domainId,
            'product' => $product,
        ]);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Category\Category
     */
    public function getProductMainCategoryOnDomain(Product $product, $domainId) {
        $productMainCategory = $this->findProductMainCategoryOnDomain($product, $domainId);
        if ($productMainCategory === null) {
            throw new \Shopsys\ShopBundle\Model\Category\Exception\CategoryNotFoundException();
        }

        return $productMainCategory;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Category\Category $category
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Category\Category[]
     */
    public function getVisibleCategoriesInPathFromRootOnDomain(Category $category, $domainId) {
        $qb = $this->getAllVisibleByDomainIdQueryBuilder($domainId)
            ->andWhere('c.lft <= :lft')->setParameter('lft', $category->getLft())
            ->andWhere('c.rgt >= :rgt')->setParameter('rgt', $category->getRgt())
            ->orderBy('c.lft');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @param  \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return string[]
     */
    public function getCategoryNamesInPathFromRootToProductMainCategoryOnDomain(Product $product, DomainConfig $domainConfig) {
        $queryBuilder = $this->getAllQueryBuilder();
        $domainId = $domainConfig->getId();
        $locale = $domainConfig->getLocale();
        $mainCategory = $this->getProductMainCategoryOnDomain($product, $domainId);

        $this->addTranslation($queryBuilder, $locale);
        $queryBuilder
            ->select('ct.name')
            ->andWhere('c.lft <= :mainCategoryLft AND c.rgt >= :mainCategoryRgt')
            ->setParameter('mainCategoryLft', $mainCategory->getLft())
            ->setParameter('mainCategoryRgt', $mainCategory->getRgt());
        $result = $queryBuilder->getQuery()->getScalarResult();

        return array_map('current', $result);
    }

    /**
     * @param int[] $categoryIds
     * @return \Shopsys\ShopBundle\Model\Category\Category[]
     */
    public function getCategoriesByIds(array $categoryIds) {
        $queryBuilder = $this->getAllQueryBuilder();
        $queryBuilder
            ->andWhere('c.id IN (:categoryIds)')
            ->setParameter('categoryIds', $categoryIds);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Category\Category[] $categories
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Category\Category[]
     */
    public function getCategoriesWithVisibleChildren(array $categories, $domainId) {
        $queryBuilder = $this->getAllVisibleByDomainIdQueryBuilder($domainId);

        $queryBuilder
            ->join(Category::class, 'cc', Join::WITH, 'cc.parent = c')
            ->andWhere('c IN (:categories)')
            ->setParameter('categories', $categories);

        return $queryBuilder->getQuery()->getResult();
    }

}
