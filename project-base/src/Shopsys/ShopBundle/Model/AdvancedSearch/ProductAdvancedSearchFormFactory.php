<?php

namespace SS6\ShopBundle\Model\AdvancedSearch;

use SS6\ShopBundle\Form\Admin\AdvancedSearch\AdvancedSearchOperatorTranslation;
use SS6\ShopBundle\Form\Admin\AdvancedSearch\AdvancedSearchProductFilterTranslation;
use SS6\ShopBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig;
use Symfony\Component\Form\FormFactoryInterface;

class ProductAdvancedSearchFormFactory extends AbstractAdvancedSearchFormFactory {

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
