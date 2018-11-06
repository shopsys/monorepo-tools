<?php

namespace Shopsys\FrameworkBundle\Model\AdvancedSearch;

use Shopsys\FrameworkBundle\Form\Admin\AdvancedSearch\AdvancedSearchOperatorTranslation;
use Shopsys\FrameworkBundle\Form\Admin\AdvancedSearch\AdvancedSearchOrderFilterTranslation;
use Symfony\Component\Form\FormFactoryInterface;

class OrderAdvancedSearchFormFactory extends AbstractAdvancedSearchFormFactory
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\AdvancedSearch\OrderAdvancedSearchConfig $orderAdvancedSearchConfig
     * @param \Shopsys\FrameworkBundle\Form\Admin\AdvancedSearch\AdvancedSearchOrderFilterTranslation $advancedSearchOrderFilterTranslation
     * @param \Symfony\Component\Form\FormFactoryInterface $formFactory
     * @param \Shopsys\FrameworkBundle\Form\Admin\AdvancedSearch\AdvancedSearchOperatorTranslation $advancedSearchOperatorTranslation
     */
    public function __construct(
        OrderAdvancedSearchConfig $orderAdvancedSearchConfig,
        AdvancedSearchOrderFilterTranslation $advancedSearchOrderFilterTranslation,
        FormFactoryInterface $formFactory,
        AdvancedSearchOperatorTranslation $advancedSearchOperatorTranslation
    ) {
        parent::__construct(
            $orderAdvancedSearchConfig,
            $advancedSearchOrderFilterTranslation,
            $formFactory,
            $advancedSearchOperatorTranslation
        );
    }
}
