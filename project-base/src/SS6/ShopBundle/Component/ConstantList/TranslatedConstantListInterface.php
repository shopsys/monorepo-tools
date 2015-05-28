<?php

namespace SS6\ShopBundle\Component\ConstantList;

interface TranslatedConstantListInterface {

	/**
	 * @return string[]
	 */
	public function getValues();

	/**
	 * @param string $constant
	 * @return string
	 */
	public function getTranslation($constant);

	/**
	 * @return string[string]
	 */
	public function getTranslationsIndexedByValue();

}
