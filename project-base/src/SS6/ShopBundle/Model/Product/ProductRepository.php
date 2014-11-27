<?php

namespace SS6\ShopBundle\Model\Product;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr\Join;
use SS6\ShopBundle\Component\Paginator\PaginationResult;
use SS6\ShopBundle\Component\Paginator\QueryPaginator;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductListOrderingSetting;

class ProductRepository {

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
	private function getProductRepository() {
		return $this->em->getRepository(Product::class);
	}

	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	private function getProductDomainRepository() {
		return $this->em->getRepository(ProductDomain::class);
	}

	/**
	 * @param int $id
	 * @return \SS6\ShopBundle\Model\Product\Product|null
	 */
	public function findById($id) {
		return $this->getProductRepository()->find($id);
	}

	/**
	 * @param int $domainId
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	private function getAllVisibleByDomainIdQueryBuilder($domainId) {
		$queryBuilder = $this->em->createQueryBuilder()
			->select('p')
			->from(Product::class, 'p')
			->join(ProductDomain::class, 'pd', Join::WITH, 'pd.product = p.id')
			->where('pd.domainId = :domainId')
				->andWhere('pd.visible = TRUE')
			->orderBy('p.id');

		$queryBuilder->setParameter('domainId', $domainId);

		return $queryBuilder;
	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $queryBuilder
	 * @param string $locale
	 */
	private function addTranslation(QueryBuilder $queryBuilder, $locale) {
		$queryBuilder->addSelect('pt')
			->join('p.translations', 'pt', Join::WITH, 'pt.locale = :locale');

		$queryBuilder->setParameter('locale', $locale);
	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $queryBuilder
	 * @param int $departmentId
	 */
	private function filterByDepartmentId(QueryBuilder $queryBuilder, $departmentId) {
		$queryBuilder->join('p.departments', 'pdep', Join::WITH, 'pdep.id = :departmentId');
		$queryBuilder->setParameter('departmentId', $departmentId);
	}

	/**
	 * @param int $domainId
	 * @param string $locale
	 * @param \SS6\ShopBundle\Model\Product\ProductListOrderingSetting $orderingSetting
	 * @param int $page
	 * @param int $limit
	 * @param int $departmentId
	 * @return PaginationResult
	 */
	public function getPaginationResultInDepartment(
		$domainId,
		$locale,
		ProductListOrderingSetting $orderingSetting,
		$page,
		$limit,
		$departmentId
	) {
		$queryBuilder = $this->getAllVisibleByDomainIdQueryBuilder($domainId);
		$this->addTranslation($queryBuilder, $locale);
		$this->filterByDepartmentId($queryBuilder, $departmentId);
		$this->applyOrdering($queryBuilder, $orderingSetting);

		$queryPaginator = new QueryPaginator($queryBuilder);

		return $queryPaginator->getResult($page, $limit);
	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $queryBuilder
	 * @param \SS6\ShopBundle\Model\Product\ProductListOrderingSetting $orderingSetting
	 */
	private function applyOrdering(QueryBuilder $queryBuilder, ProductListOrderingSetting $orderingSetting) {
		switch ($orderingSetting->getOrderingMode()) {
			case ProductListOrderingSetting::ORDER_BY_NAME_ASC:
				$queryBuilder->orderBy('pt.name', 'asc');
				break;

			case ProductListOrderingSetting::ORDER_BY_NAME_DESC:
				$queryBuilder->orderBy('pt.name', 'desc');
				break;

			default:
				$message = 'Product list ordering mod "' . $orderingSetting->getOrderingMode()  .'" is not supported.';
				throw new \SS6\ShopBundle\Model\ProductException\InvalidOrderingModeException($message);
		}

		$queryBuilder->addOrderBy('p.id', 'asc');
	}

	/**
	 * @param int $id
	 * @return \SS6\ShopBundle\Model\Product\Product
	 * @throws \SS6\ShopBundle\Model\Product\Exception\ProductNotFoundException
	 */
	public function getById($id) {
		$product = $this->findById($id);

		if ($product === null) {
			throw new \SS6\ShopBundle\Model\Product\Exception\ProductNotFoundException('Product with ID ' . $id . ' does not exist.');
		}

		return $product;
	}

	/**
	 * @param int $id
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	public function getVisibleByIdAndDomainId($id, $domainId) {
		$qb = $this->getAllVisibleByDomainIdQueryBuilder($domainId);
		$qb->andWhere('p.id = :productId');
		$qb->setParameter('productId', $id);

		$product = $qb->getQuery()->getOneOrNullResult();

		if ($product === null) {
			throw new \SS6\ShopBundle\Model\Product\Exception\ProductNotFoundException($qb->getDQL());
		}

		return $product;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat $vat
	 * @return \SS6\ShopBundle\Model\Product\Product[]
	 */
	public function getAllByVat(Vat $vat) {
		return $this->getProductRepository()->findBy(array('vat' => $vat));
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return \SS6\ShopBundle\Model\Product\ProductDomain[]
	 */
	public function getProductDomainsByProduct(Product $product) {
		return $this->getProductDomainRepository()->findBy(array(
			'product' => $product,
		));
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Product\ProductDomain|null
	 */
	public function findProductDomainByProductAndDomainId(Product $product, $domainId) {
		return $this->getProductDomainRepository()->findOneBy(array(
			'product' => $product,
			'domainId' => $domainId,
		));
	}

	/**
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Product[]
	 */
	public function getVisibleProductsByDomainId($domainId) {
		return $this->getAllVisibleByDomainIdQueryBuilder($domainId)->getQuery()->getResult();
	}

}
