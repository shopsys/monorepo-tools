<?php

namespace SS6\ShopBundle\Form\Admin\AdvancedSearch;

class AdvancedSearchOrderFilterTranslation extends AdvancedSearchFilterTranslation {

	public function __construct() {
		parent::__construct();

		$this->addFilterTranslation('orderNumber', t('Číslo objednávky'));
		$this->addFilterTranslation('orderCreatedAt', t('Vytvořeno dne'));
		$this->addFilterTranslation('orderTotalPriceWithVat', t('Cena s DPH'));
		$this->addFilterTranslation('orderDomain', t('Doména'));
		$this->addFilterTranslation('orderStatus', t('Stav objednávky'));
		$this->addFilterTranslation('orderProduct', t('Zboží v objednávce'));
	}

}
