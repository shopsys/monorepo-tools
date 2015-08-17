<?php

namespace SS6\ShopBundle\Model\Product\Search;

use Doctrine\ORM\QueryBuilder;
use SS6\ShopBundle\Component\Fulltext\TsqueryFactory;
use SS6\ShopBundle\Component\String\DatabaseSearching;

class ProductSearchRepository {

	/**
	 * @var \SS6\ShopBundle\Component\Fulltext\TsqueryFactory
	 */
	private $tsqueryFactory;

	public function __construct(TsqueryFactory $tsqueryFactory) {
		$this->tsqueryFactory = $tsqueryFactory;
	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $productQueryBuilder
	 * @param string|null $searchText
	 */
	public function filterBySearchText(QueryBuilder $productQueryBuilder, $searchText) {
		$productQueryBuilder
			->andWhere('TSQUERY(pd.fulltextTsvector, :tsquery) = TRUE')
			->setParameter('tsquery', $this->tsqueryFactory->getTsqueryWithAndConditions($searchText));
	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $productQueryBuilder
	 * @param string|null $searchText
	 */
	public function addRelevance(QueryBuilder $productQueryBuilder, $searchText) {
		$productQueryBuilder->addSelect('
			CASE
				WHEN pt.name LIKE :searchTextLike THEN 1
				WHEN TSQUERY(pt.nameTsvector, :searchTextTsqueryAnd) = TRUE THEN 2
				WHEN TSQUERY(p.catnumTsvector, :searchTextTsqueryOr) = TRUE THEN 3
				WHEN TSQUERY(p.partnoTsvector, :searchTextTsqueryOr) = TRUE THEN 4
				WHEN pd.description LIKE :searchTextLike THEN 5
				WHEN TSQUERY(pt.nameTsvector, :searchTextTsqueryOr) = TRUE THEN 6
				ELSE 7
			END AS HIDDEN relevance
		');

		$productQueryBuilder->setParameter(
			'searchTextLike',
			'%' . DatabaseSearching::getLikeSearchString($searchText) . '%'
		);

		$productQueryBuilder->setParameter(
			'searchTextTsqueryAnd',
			$this->tsqueryFactory->getTsqueryWithAndConditions($searchText)
		);

		$productQueryBuilder->setParameter(
			'searchTextTsqueryOr',
			$this->tsqueryFactory->getTsqueryWithOrConditions($searchText)
		);
	}

}
