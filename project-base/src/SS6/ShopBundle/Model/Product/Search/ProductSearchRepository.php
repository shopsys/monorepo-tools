<?php

namespace SS6\ShopBundle\Model\Product\Search;

use Doctrine\ORM\QueryBuilder;
use SS6\ShopBundle\Component\Fulltext\TsqueryFactory;

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

}
