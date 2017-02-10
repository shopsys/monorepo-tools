<?php

namespace Shopsys\ShopBundle\Model\AdvancedSearch;

use Shopsys\ShopBundle\Form\Admin\AdvancedSearch\AdvancedSearchOperatorTranslation;
use Shopsys\ShopBundle\Form\Admin\AdvancedSearch\AdvancedSearchProductFilterTranslation;
use Shopsys\ShopBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig;
use Symfony\Component\Form\FormFactoryInterface;

class ProductAdvancedSearchFormFactory extends AbstractAdvancedSearchFormFactory
{
    public function __construct(
        ProductAdvancedSearchConfig $productAdvancedSearchConfig,
        AdvancedSearchProductFilterTranslation $advancedSearchProductFilterTranslation,
        FormFactoryInterface $formFactory,
        AdvancedSearchOperatorTranslation $advancedSearchOperatorTranslation
    ) {
        parent::__construct(
            $productAdvancedSearchConfig,
            $advancedSearchProductFilterTranslation,
            $formFactory,
            $advancedSearchOperatorTranslation
        );
    }
}
