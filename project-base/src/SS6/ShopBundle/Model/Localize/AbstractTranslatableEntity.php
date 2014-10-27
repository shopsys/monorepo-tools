<?php

namespace SS6\ShopBundle\Model\Localize;

use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslatable;

abstract class AbstractTranslatableEntity extends AbstractTranslatable {

	/**
	 * @Prezent\CurrentLocale
	 */
	protected $currentLocale;

	/**
	 * @param string $locale
	 * @return \Prezent\Doctrine\Translatable\TranslationInterface|null
	 */
	protected function findTranslation($locale) {
		foreach ($this->getTranslations() as $translation) {
			if ($translation->getLocale() === $locale) {
				return $translation;
			}
		}

		return null;
	}
}
