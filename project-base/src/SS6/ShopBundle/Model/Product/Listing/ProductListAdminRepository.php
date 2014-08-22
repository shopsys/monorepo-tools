<?php

namespace SS6\ShopBundle\Model\Product\Listing;

use Doctrine\ORM\QueryBuilder;
use SS6\ShopBundle\Model\String\DatabaseSearching;

class ProductListAdminRepository {

	/**
	 * @param \Doctrine\ORM\QueryBuilder $queryBuilder
	 * @param type $searchData
	 */
	public function extendQueryBuilderByQuickSearchData(QueryBuilder $queryBuilder, $searchData) {
		if ($searchData['text'] !== null && $searchData['text'] !== '') {
			$queryBuilder->andWhere('
				(
					p.name LIKE :text OR
					p.catnum LIKE :text OR
					p.partno LIKE :text
				)');
			$querySerachText = '%' . DatabaseSearching::getQuerySearchString($searchData['text']) . '%';
			$queryBuilder->setParameter('text', $querySerachText);
		}
	}
}
