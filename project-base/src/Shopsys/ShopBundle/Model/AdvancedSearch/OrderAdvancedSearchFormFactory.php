<?php

namespace Shopsys\FrameworkBundle\Model\AdvancedSearch;

use Shopsys\FrameworkBundle\Form\Admin\AdvancedSearch\AdvancedSearchOperatorTranslation;
use Shopsys\FrameworkBundle\Form\Admin\AdvancedSearch\AdvancedSearchOrderFilterTranslation;
use Symfony\Component\Form\FormFactoryInterface;

class OrderAdvancedSearchFormFactory extends AbstractAdvancedSearchFormFactory
{
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
