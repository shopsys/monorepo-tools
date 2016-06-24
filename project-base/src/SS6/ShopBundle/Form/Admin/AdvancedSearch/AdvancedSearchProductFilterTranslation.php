<?php

namespace SS6\ShopBundle\Form\Admin\AdvancedSearch;

class AdvancedSearchProductFilterTranslation extends AdvancedSearchFilterTranslation {

	public function __construct() {
		parent::__construct();

		$this->addFilterTranslation('productCatnum', t('Katalogové číslo'));
		$this->addFilterTranslation('productFlag', t('Příznak'));
		$this->addFilterTranslation('productName', t('Název produktu'));
		$this->addFilterTranslation('productPartno', t('Partno'));
		$this->addFilterTranslation('productStock', t('Skladové zásoby'));
		$this->addFilterTranslation('productCalculatedSellingDenied', t('Vyřazeno z prodeje'));
	}

}
