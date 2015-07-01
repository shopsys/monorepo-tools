<?php

namespace SS6\ShopBundle\Form\Admin\AdvancedSearch;

use SS6\ShopBundle\Component\Translation\Translator;

class AdvancedSearchProductFilterTranslation extends AdvancedSearchFilterTranslation {

	public function __construct(Translator $translator) {
		parent::__construct();

		$this->addFilterTranslation('productCatnum', $translator->trans('Katalogové číslo'));
		$this->addFilterTranslation('productName', $translator->trans('Název produktu'));
		$this->addFilterTranslation('productPartno', $translator->trans('Partno'));
		$this->addFilterTranslation('productStock', $translator->trans('Skladové zásoby'));
	}

}
