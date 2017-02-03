<?php

namespace SS6\ShopBundle\Form\Admin\AdvancedSearch;

class AdvancedSearchProductFilterTranslation extends AdvancedSearchFilterTranslation {

	public function __construct() {
		parent::__construct();

		$this->addFilterTranslation('productCatnum', t('Katalogové číslo'));
		$this->addFilterTranslation('productFlag', t('Příznak'));
		$this->addFilterTranslation('productName', t('Název produktu'));
		$this->addFilterTranslation('productPartno', t('PartNo (výrobní číslo)'));
		$this->addFilterTranslation('productStock', t('Skladové zásoby'));
		$this->addFilterTranslation('productCalculatedSellingDenied', t('Vyřazeno z prodeje'));
		$this->addFilterTranslation('productAvailability', t('Dostupnost'));
		$this->addFilterTranslation('productBrand', t('Značka'));
	}

}
