<?php

namespace Shopsys\ShopBundle\Model\AdvancedSearch;

use Shopsys\ShopBundle\Form\Admin\AdvancedSearch\AdvancedSearchOperatorTranslation;
use Shopsys\ShopBundle\Form\Admin\AdvancedSearch\AdvancedSearchOrderFilterTranslation;
use Shopsys\ShopBundle\Model\AdvancedSearch\OrderAdvancedSearchConfig;
use Symfony\Component\Form\FormFactoryInterface;

class OrderAdvancedSearchFormFactory extends AbstractAdvancedSearchFormFactory {

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
