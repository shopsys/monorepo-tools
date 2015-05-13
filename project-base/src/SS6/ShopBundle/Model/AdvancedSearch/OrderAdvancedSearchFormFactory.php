<?php

namespace SS6\ShopBundle\Model\AdvancedSearch;

use SS6\ShopBundle\Form\Admin\AdvancedSearch\AdvancedSearchOperatorTranslation;
use SS6\ShopBundle\Form\Admin\AdvancedSearch\AdvancedSearchOrderFilterTranslation;
use SS6\ShopBundle\Model\AdvancedSearch\OrderAdvancedSearchConfig;
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
