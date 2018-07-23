<?php

namespace Shopsys\FrameworkBundle\Model\Category;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;
use Shopsys\FrameworkBundle\Component\Paginator\QueryPaginator;
use Shopsys\FrameworkBundle\Component\String\DatabaseSearching;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomain;
use Shopsys\FrameworkBundle\Model\Product\ProductRepository;

class CategoryRepository extends NestedTreeRepository
{
    const MOVE_DOWN_TO_BOTTOM = true;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductRepository
     */
    protected $productRepository;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductRepository $productRepository
     * @param \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver $entityNameResolver
     */
    public function __construct(
        EntityManagerInterface $em,
        ProductRepository $productRepository,
        EntityNameResolver $entityNameResolver
    ) {
        $this->em = $em;
        $this->productRepository = $productRepository;

        $resolvedClassName = $entityNameResolver->resolve(Category::class);
        $classMetadata = $this->em->getClassMetadata($resolvedClassName);
        parent::__construct($this->em, $classMetadata);
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getCategoryRepository()
    {
        return $this->em->getRepository(Category::class);
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getAllQueryBuilder()
    {
        return $this->getCategoryRepository()
            ->createQueryBuilder('c')
            ->where('c.parent IS NOT NULL')
            ->orderBy('c.lft');
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Category\Category[]
     */
    public function getAll()
    {
        return $this->getAllQueryBuilder()
            ->getQuery()
            ->getResult();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category[] $selectedCategories
     * @return \Shopsys\FrameworkBundle\Model\Category\Category[]
     */
    public function getAllCategoriesOfCollapsedTree(array $selectedCategories)
    {
        $openedParentsQueryBuilder = $this->getCategoryRepository()
            ->createQueryBuilder('c')
            ->select('c.id')
            ->where('c.parent IS NULL');

        foreach ($selectedCategories as $selectedCategory) {
            $where = sprintf('c.lft < %d AND c.rgt > %d', $selectedCategory->getLft(), $selectedCategory->getRgt());
            $openedParentsQueryBuilder->orWhere($where);
        }

        $openedParentIds = array_column($openedParentsQueryBuilder->getQuery()->getScalarResult(), 'id');

        return $this->getAllQueryBuilder()
            ->select('c, cd, ct')
            ->join('c.domains', 'cd')
            ->join('c.translations', 'ct')
            ->where('c.parent IN (:openedParentIds)')
            ->setParameter('openedParentIds', $openedParentIds)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $domainId
     * @param string $locale
     * @return \Shopsys\FrameworkBundle\Model\Category\Category[]
     */
    public function getFullPathsIndexedByIdsForDomain($domainId, $locale)
    {
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
    public function getAllIds()
    {
        $result = $this->getAllQueryBuilder()
            ->select('c.id')
            ->getQuery()
            ->getScalarResult();

        return array_map('current', $result);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Category\Category
     */
    public function getRootCategory()
    {
        $rootCategory = $this->getCategoryRepository()->findOneBy(['parent' => null]);

        if ($rootCategory === null) {
            $message = 'Root category not found';
            throw new \Shopsys\FrameworkBundle\Model\Category\Exception\RootCategoryNotFoundException($message);
        }

        return $rootCategory;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $categoryBranch
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return \Shopsys\FrameworkBundle\Model\Category\Category[]
     */
    public function getTranslatedAllWithoutBranch(Category $categoryBranch, DomainConfig $domainConfig)
    {
        $queryBuilder = $this->getAllQueryBuilder();
        $this->addTranslation($queryBuilder, $domainConfig->getLocale());

        return $queryBuilder->andWhere('c.lft < :branchLft OR c.rgt > :branchRgt')
            ->setParameter('branchLft', $categoryBranch->getLft())
            ->setParameter('branchRgt', $categoryBranch->getRgt())
            ->getQuery()
            ->execute();
    }

    /**
     * @param int $categoryId
     * @return \Shopsys\FrameworkBundle\Model\Category\Category|null
     */
    public function findById($categoryId)
    {
        $category = $this->getCategoryRepository()->find($categoryId);
        /* @var $category \Shopsys\FrameworkBundle\Model\Category\Category */

        if ($category !== null && $category->getParent() === null) {
            // Copies logic from getAllQueryBuilder() - excludes root category
            // Query builder is not used to be able to get the category from identity map if it was loaded previously
            return null;
        }

        return $category;
    }

    /**
     * @param int $categoryId
     * @return \Shopsys\FrameworkBundle\Model\Category\Category
     */
    public function getById($categoryId)
    {
        $category = $this->findById($categoryId);

        if ($category === null) {
            $message = 'Category with ID ' . $categoryId . ' not found.';
            throw new \Shopsys\FrameworkBundle\Model\Category\Exception\CategoryNotFoundException($message);
        }

        return $category;
    }

    /**
     * @param string $locale
     * @return \Shopsys\FrameworkBundle\Model\Category\Category[]
     */
    public function getPreOrderTreeTraversalForAllCategories($locale)
    {
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
     * @return \Shopsys\FrameworkBundle\Model\Category\Category[]
     */
    public function getPreOrderTreeTraversalForVisibleCategoriesByDomain($domainId, $locale)
    {
        $queryBuilder = $this->getPreOrderTreeTraversalForAllCategoriesByDomainQueryBuilder($domainId, $locale);

        $queryBuilder->andWhere('cd.visible = TRUE');

        return $queryBuilder->getQuery()->execute();
    }

    /**
     * @param int $domainId
     * @param string $locale
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getPreOrderTreeTraversalForAllCategoriesByDomainQueryBuilder($domainId, $locale)
    {
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
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $parentCategory
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return \Shopsys\FrameworkBundle\Model\Category\Category[]
     */
    public function getTranslatedVisibleSubcategoriesByDomain(Category $parentCategory, DomainConfig $domainConfig)
    {
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
    protected function addTranslation(QueryBuilder $categoriesQueryBuilder, $locale)
    {
        $categoriesQueryBuilder
            ->addSelect('ct')
            ->join('c.translations', 'ct', Join::WITH, 'ct.locale = :locale')
            ->setParameter('locale', $locale);
    }

    /**
     * @param string|null $searchText
     * @param int $domainId
     * @param string $locale
     * @param int $page
     * @param int $limit
     * @return \Shopsys\FrameworkBundle\Component\Paginator\PaginationResult
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
     * @return \Shopsys\FrameworkBundle\Model\Category\Category[]
     */
    public function getVisibleByDomainIdAndSearchText($domainId, $locale, $searchText)
    {
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
    protected function getVisibleByDomainIdAndSearchTextQueryBuilder(
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
    public function getAllVisibleByDomainIdQueryBuilder($domainId)
    {
        $queryBuilder = $this->getAllQueryBuilder()
            ->join(CategoryDomain::class, 'cd', Join::WITH, 'cd.category = c.id')
            ->andWhere('cd.domainId = :domainId')
            ->andWhere('cd.visible = TRUE');

        $queryBuilder->setParameter('domainId', $domainId);

        return $queryBuilder;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $category
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Category\Category[]
     */
    public function getAllVisibleChildrenByCategoryAndDomainId(Category $category, $domainId)
    {
        $queryBuilder = $this->getAllVisibleByDomainIdQueryBuilder($domainId)
            ->andWhere('c.parent = :category')
            ->setParameter('category', $category);

        return $queryBuilder->getQuery()->execute();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category[] $categories
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @param int $domainId
     * @return int[]
     */
    public function getListableProductCountsIndexedByCategoryId(
        array $categories,
        PricingGroup $pricingGroup,
        $domainId
    ) {
        if (count($categories) === 0) {
            return [];
        }
        $listableProductCountsIndexedByCategoryId = [];
        foreach ($categories as $category) {
            // Initialize array with zeros as categories without found products will not be represented in result rows
            $listableProductCountsIndexedByCategoryId[$category->getId()] = 0;
        }

        $queryBuilder = $this->productRepository->getAllListableQueryBuilder($domainId, $pricingGroup)
            ->join(
                ProductCategoryDomain::class,
                'pcd',
                Join::WITH,
                'pcd.product = p
                 AND pcd.category IN (:categories)
                 AND pcd.domainId = :domainId'
            )
            ->select('IDENTITY(pcd.category) AS categoryId, COUNT(p) AS productCount')
            ->setParameter('categories', $categories)
            ->setParameter('domainId', $domainId)
            ->groupBy('pcd.category')
            ->resetDQLPart('orderBy');

        foreach ($queryBuilder->getQuery()->getArrayResult() as $result) {
            $listableProductCountsIndexedByCategoryId[$result['categoryId']] = $result['productCount'];
        }

        return $listableProductCountsIndexedByCategoryId;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param string|null $searchText
     */
    protected function filterBySearchText(QueryBuilder $queryBuilder, $searchText)
    {
        $queryBuilder->andWhere(
            'NORMALIZE(ct.name) LIKE NORMALIZE(:searchText)'
        );
        $queryBuilder->setParameter('searchText', DatabaseSearching::getFullTextLikeSearchString($searchText));
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Category\Category|null
     */
    public function findProductMainCategoryOnDomain(Product $product, $domainId)
    {
        $qb = $this->getAllVisibleByDomainIdQueryBuilder($domainId)
            ->join(
                ProductCategoryDomain::class,
                'pcd',
                Join::WITH,
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
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Category\Category
     */
    public function getProductMainCategoryOnDomain(Product $product, $domainId)
    {
        $productMainCategory = $this->findProductMainCategoryOnDomain($product, $domainId);
        if ($productMainCategory === null) {
            throw new \Shopsys\FrameworkBundle\Model\Category\Exception\CategoryNotFoundException();
        }

        return $productMainCategory;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $category
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Category\Category[]
     */
    public function getVisibleCategoriesInPathFromRootOnDomain(Category $category, $domainId)
    {
        $qb = $this->getAllVisibleByDomainIdQueryBuilder($domainId)
            ->andWhere('c.lft <= :lft')->setParameter('lft', $category->getLft())
            ->andWhere('c.rgt >= :rgt')->setParameter('rgt', $category->getRgt())
            ->orderBy('c.lft');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param  \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return string[]
     */
    public function getCategoryNamesInPathFromRootToProductMainCategoryOnDomain(Product $product, DomainConfig $domainConfig)
    {
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
     * @return \Shopsys\FrameworkBundle\Model\Category\Category[]
     */
    public function getCategoriesByIds(array $categoryIds)
    {
        $queryBuilder = $this->getAllQueryBuilder();
        $queryBuilder
            ->andWhere('c.id IN (:categoryIds)')
            ->setParameter('categoryIds', $categoryIds);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category[] $categories
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Category\Category[]
     */
    public function getCategoriesWithVisibleChildren(array $categories, $domainId)
    {
        $queryBuilder = $this->getAllVisibleByDomainIdQueryBuilder($domainId);

        $queryBuilder
            ->join(Category::class, 'cc', Join::WITH, 'cc.parent = c')
            ->join(CategoryDomain::class, 'ccd', Join::WITH, 'ccd.category = cc.id')
            ->andWhere('ccd.domainId = :domainId')
            ->andWhere('ccd.visible = TRUE')
            ->andWhere('c IN (:categories)')
            ->setParameter('categories', $categories);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param  \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return \Shopsys\FrameworkBundle\Model\Category\Category[]
     */
    public function getTranslatedAll(DomainConfig $domainConfig)
    {
        $queryBuilder = $this->getAllQueryBuilder();
        $this->addTranslation($queryBuilder, $domainConfig->getLocale());

        return $queryBuilder->getQuery()
            ->getResult();
    }
}
