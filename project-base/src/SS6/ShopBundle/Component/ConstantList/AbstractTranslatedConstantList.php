<?php

namespace SS6\ShopBundle\Component\ConstantList;

use SS6\ShopBundle\Component\ConstantList\TranslatedConstantListInterface;
use SS6\ShopBundle\Component\Translation\Translator;

abstract class AbstractTranslatedConstantList implements TranslatedConstantListInterface {

	/**
	 * @var \Symfony\Component\Translation\TranslatorInterface
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
	 * @param string $constant
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
