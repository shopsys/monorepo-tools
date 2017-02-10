<?php

namespace Shopsys\ShopBundle\Model\Product\MassAction;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Shopsys\ShopBundle\Model\Product\ProductHiddenRecalculator;
use Shopsys\ShopBundle\Model\Product\ProductVisibilityFacade;

class ProductMassActionFacade
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\MassAction\ProductMassActionRepository
     */
    private $productMassActionRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductVisibilityFacade
     */
    private $productVisibilityFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductHiddenRecalculator
     */
    private $productHiddenRecalculator;

    public function __construct(
        EntityManager $em,
        ProductMassActionRepository $productMassActionRepository,
        ProductVisibilityFacade $productVisibilityFacade,
        ProductHiddenRecalculator $productHiddenRecalculator
    ) {
        $this->em = $em;
        $this->productMassActionRepository = $productMassActionRepository;
        $this->productVisibilityFacade = $productVisibilityFacade;
        $this->productHiddenRecalculator = $productHiddenRecalculator;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\MassAction\ProductMassActionData $productMassActionData
     * @param \Doctrine\ORM\QueryBuilder $selectQueryBuilder
     * @param int[] $checkedProductIds
     */
    public function doMassAction(
        ProductMassActionData $productMassActionData,
        QueryBuilder $selectQueryBuilder,
        array $checkedProductIds
    ) {
        $selectedProductIds = $this->getSelectedProductIds(
            $productMassActionData,
            $selectQueryBuilder,
            $checkedProductIds
        );

        if ($productMassActionData->action === ProductMassActionData::ACTION_SET) {
            if ($productMassActionData->subject === ProductMassActionData::SUBJECT_PRODUCT_HIDDEN) {
                $this->productMassActionRepository->setHidden(
                    $selectedProductIds,
                    $productMassActionData->value === ProductMassActionData::VALUE_PRODUCT_HIDE
                );
                $this->productHiddenRecalculator->calculateHiddenForAll();
                $this->productVisibilityFacade->refreshProductsVisibilityForMarkedDelayed();
            }
        }
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\MassAction\ProductMassActionData $productMassActionData
     * @param \Doctrine\ORM\QueryBuilder $selectQueryBuilder
     * @param int[] $checkedProductIds
     * @return int[]
     */
    private function getSelectedProductIds(
        ProductMassActionData $productMassActionData,
        QueryBuilder $selectQueryBuilder,
        array $checkedProductIds
    ) {
        $selectedProductIds = [];

        if ($productMassActionData->selectType === ProductMassActionData::SELECT_TYPE_ALL_RESULTS) {
            $queryBuilder = clone $selectQueryBuilder;

            $results = $queryBuilder
                ->select('p.id')
                ->getQuery()
                ->getScalarResult();

            foreach ($results as $result) {
                $selectedProductIds[] = $result['id'];
            }
        } elseif ($productMassActionData->selectType === ProductMassActionData::SELECT_TYPE_CHECKED) {
            $selectedProductIds = $checkedProductIds;
        } else {
            throw new \Shopsys\ShopBundle\Model\Product\MassAction\Exception\UnsupportedSelectionType($productMassActionData->selectType);
        }

        return $selectedProductIds;
    }

}
