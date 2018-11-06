<?php

namespace Shopsys\FrameworkBundle\Model\AdvancedSearch;

use Shopsys\FrameworkBundle\Form\Admin\AdvancedSearch\AdvancedSearchOperatorTranslation;
use Shopsys\FrameworkBundle\Form\Admin\AdvancedSearch\AdvancedSearchProductFilterTranslation;
use Symfony\Component\Form\FormFactoryInterface;

class ProductAdvancedSearchFormFactory extends AbstractAdvancedSearchFormFactory
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig $productAdvancedSearchConfig
     * @param \Shopsys\FrameworkBundle\Form\Admin\AdvancedSearch\AdvancedSearchProductFilterTranslation $advancedSearchProductFilterTranslation
     * @param \Symfony\Component\Form\FormFactoryInterface $formFactory
     * @param \Shopsys\FrameworkBundle\Form\Admin\AdvancedSearch\AdvancedSearchOperatorTranslation $advancedSearchOperatorTranslation
     */
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
