<?php

namespace Shopsys\ShopBundle\Model\Product\Search;

use Doctrine\ORM\QueryBuilder;
use Shopsys\ShopBundle\Component\Fulltext\TsqueryFactory;
use Shopsys\ShopBundle\Component\String\DatabaseSearching;

class ProductSearchRepository
{
    /**
     * @var \Shopsys\ShopBundle\Component\Fulltext\TsqueryFactory
     */
    private $tsqueryFactory;

    public function __construct(TsqueryFactory $tsqueryFactory)
    {
        $this->tsqueryFactory = $tsqueryFactory;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $productQueryBuilder
     * @param string|null $searchText
     */
    public function filterBySearchText(QueryBuilder $productQueryBuilder, $searchText)
    {
        if ($this->tsqueryFactory->isValidSearchText($searchText)) {
            $productQueryBuilder
                ->andWhere('TSQUERY(pd.fulltextTsvector, :fulltextQuery) = TRUE')
                ->setParameter(
                    'fulltextQuery',
                    $this->tsqueryFactory->getTsqueryWithAndConditionsAndPrefixMatchForLastWord($searchText)
                );
        } else {
            $productQueryBuilder->andWhere('TRUE = FALSE');
        }
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $productQueryBuilder
     * @param string|null $searchText
     */
    public function addRelevance(QueryBuilder $productQueryBuilder, $searchText)
    {
        $productQueryBuilder->addSelect('
            CASE
                WHEN (
                    NORMALIZE(pt.name) LIKE NORMALIZE(:searchTextLikeWithWildcardOnLeftAndSpaceAndWildcardOnRight)
                    OR
                    NORMALIZE(pt.name) LIKE NORMALIZE(:searchTextLikeWithWildcardOnLeft)
                ) THEN 1
                WHEN TSQUERY(pt.nameTsvector, :searchTextTsqueryAnd) = TRUE THEN 2
                WHEN TSQUERY(p.catnumTsvector, :searchTextTsqueryOr) = TRUE THEN 3
                WHEN TSQUERY(p.partnoTsvector, :searchTextTsqueryOr) = TRUE THEN 4
                WHEN NORMALIZE(pt.name) LIKE NORMALIZE(:searchTextLikeWithWildcardsOnBothSides) THEN 5
                WHEN TSQUERY(pt.nameTsvector, :searchTextTsqueryAndWithPrefixMatchForLastWord) = TRUE THEN 6
                WHEN TSQUERY(pd.descriptionTsvector, :searchTextTsqueryAnd) = TRUE THEN 7
                WHEN TSQUERY(pt.nameTsvector, :searchTextTsqueryOr) = TRUE THEN 8
                WHEN TSQUERY(pt.nameTsvector, :searchTextTsqueryOrWithPrefixMatchForLastWord) = TRUE THEN 9
                ELSE 10
            END AS HIDDEN relevance
        ');

        $productQueryBuilder->setParameter(
            'searchTextLikeWithWildcardsOnBothSides',
            DatabaseSearching::getFullTextLikeSearchString($searchText)
        );

        $productQueryBuilder->setParameter(
            'searchTextLikeWithWildcardOnLeft',
            '%' . DatabaseSearching::getLikeSearchString($searchText)
        );

        $productQueryBuilder->setParameter(
            'searchTextLikeWithWildcardOnLeftAndSpaceAndWildcardOnRight',
            '%' . DatabaseSearching::getLikeSearchString($searchText) . ' %'
        );

        $productQueryBuilder->setParameter(
            'searchTextTsqueryAnd',
            $this->tsqueryFactory->getTsqueryWithAndConditions($searchText)
        );

        $productQueryBuilder->setParameter(
            'searchTextTsqueryAndWithPrefixMatchForLastWord',
            $this->tsqueryFactory->getTsqueryWithAndConditionsAndPrefixMatchForLastWord($searchText)
        );

        $productQueryBuilder->setParameter(
            'searchTextTsqueryOr',
            $this->tsqueryFactory->getTsqueryWithOrConditions($searchText)
        );

        $productQueryBuilder->setParameter(
            'searchTextTsqueryOrWithPrefixMatchForLastWord',
            $this->tsqueryFactory->getTsqueryWithOrConditionsAndPrefixMatchForLastWord($searchText)
        );
    }
}
