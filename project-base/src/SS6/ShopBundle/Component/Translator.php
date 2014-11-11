<?php

namespace SS6\ShopBundle\Component;

use Symfony\Bundle\FrameworkBundle\Translation\Translator as BaseTranslator;

class Translator extends BaseTranslator {

	const DEFAULT_DOMAIN = 'messages';
	const NOT_TRANSLATED_PREFIX = '##';

	/**
	 * @param string $locale
	 * @return \Symfony\Component\Translation\MessageCatalogue
	 */
	public function getCatalogue($locale) {
		if (!isset($this->catalogues[$locale])) {
			$this->loadCatalogue($locale);
		}

		return $this->catalogues[$locale];
	}

}
