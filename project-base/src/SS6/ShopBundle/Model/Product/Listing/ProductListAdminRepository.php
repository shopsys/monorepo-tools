<?php

namespace SS6\ShopBundle\Model\Product\Listing;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use SS6\ShopBundle\Component\String\DatabaseSearching;
use SS6\ShopBundle\Model\Localization\Localization;
use SS6\ShopBundle\Model\Product\Product;

class ProductListAdminRepository {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Localization\Localization
	 */
	private $localization;

	public function __construct(EntityManager $em, Localization $localization) {
		$this->em = $em;
		$this->localization = $localization;
	}

	/**
	 * @param array|null $searchData
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getQueryBuilderByQuickSearchData(array $searchData = null) {
		$queryBuilder = $this->em->createQueryBuilder();
		$queryBuilder
			->select('p, pt')
			->from(Product::class, 'p')
			->join('p.translations', 'pt', Join::WITH, 'pt.locale = :locale')
			->setParameter('locale', $this->localization->getDefaultLocale());
		$this->extendQueryBuilderByQuickSearchData($queryBuilder, $searchData);

		return $queryBuilder;
	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $queryBuilder
	 * @param type $searchData
	 */
	private function extendQueryBuilderByQuickSearchData(QueryBuilder $queryBuilder, $searchData) {
		if ($searchData['text'] !== null && $searchData['text'] !== '') {
			$queryBuilder->andWhere('
				(
					pt.name LIKE :text OR
					p.catnum LIKE :text OR
					p.partno LIKE :text
				)');
			$querySerachText = '%' . DatabaseSearching::getLikeSearchString($searchData['text']) . '%';
			$queryBuilder->setParameter('text', $querySerachText);
		}
	}

}
