<?php

namespace SS6\ShopBundle\Model\Product\Filter;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use SS6\ShopBundle\Model\Category\Category;
use SS6\ShopBundle\Model\Product\Flag\Flag;
use SS6\ShopBundle\Model\Product\ProductRepository;

class FlagFilterRepository {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	public function __construct(
		EntityManager $em,
		ProductRepository $productRepository
	) {
		$this->em = $em;
		$this->productRepository = $productRepository;
	}

	/**
	 * @param type $domainId
	 * @param \SS6\ShopBundle\Model\Category\Category $category
	 * @return \SS6\ShopBundle\Model\Product\Flag\Flag[]
	 */
	public function getFlagFilterChoicesInCategory($domainId, Category $category) {
		$productsQueryBuilder = $this->productRepository->getVisibleByDomainIdAndCategoryQueryBuilder($domainId, $category);
		$productsQueryBuilder
			->join('p.flags', 'pf', Join::WITH, 'pf.id = f.id');

		$flagQueryBuilder = $this->em->createQueryBuilder()
			->select('f')
			->from(Flag::class, 'f');
		$flagQueryBuilder->andWhere($flagQueryBuilder->expr()->exists($productsQueryBuilder));

		foreach ($productsQueryBuilder->getParameters() as $parameter) {
			$flagQueryBuilder->setParameter($parameter->getName(), $parameter->getValue());
		}

		$flagsInCategory = $flagQueryBuilder->getQuery()->execute();

		return $flagsInCategory;
	}

}
