<?php

namespace SS6\ShopBundle\Form\Admin\AdvancedSearch;

use SS6\ShopBundle\Component\Translation\Translator;

class AdvancedSearchOrderFilterTranslation extends AdvancedSearchFilterTranslation {

	public function __construct(Translator $translator) {
		parent::__construct();

		$this->addFilterTranslation('orderNumber', $translator->trans('Číslo objednávky'));
		$this->addFilterTranslation('orderCreatedAt', $translator->trans('Vytvořeno dne'));
		$this->addFilterTranslation('orderTotalPriceWithVat', $translator->trans('Cena s DPH'));
		$this->addFilterTranslation('orderDomain', $translator->trans('Doména'));
		$this->addFilterTranslation('orderStatus', $translator->trans('Stav objednávky'));
		$this->addFilterTranslation('orderProduct', $translator->trans('Zboží v objednávce'));
	}

}
