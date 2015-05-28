<?php

namespace SS6\ShopBundle\Component\ConstantList;

use SS6\ShopBundle\Component\ConstantList\TranslatedConstantListInterface;
use Symfony\Component\Translation\Translator;

abstract class AbstractTranslatedConstantList implements TranslatedConstantListInterface {

	/**
	 * @var \SS6\ShopBundle\Component\Translation\Translator
	 */
	protected $translator;

	public function __construct(Translator $translator) {
		$this->translator = $translator;
	}

	/**
	 * @return string
	 */
	public function getValues() {
		return array_keys($this->getTranslationsIndexedByValue());
	}

	/**
	 * @param type $constant
	 */
	public function getTranslation($constant) {
		$translations = $this->getTranslationsIndexedByValue();
		if (!array_key_exists($constant, $translations)) {
			throw new \SS6\ShopBundle\Component\ConstantList\Exception\UndefinedTranslationException($constant);
		}

		return $translations[$constant];
	}

	/**
	 * @return string[string]
	 */
	abstract public function getTranslationsIndexedByValue();

}
