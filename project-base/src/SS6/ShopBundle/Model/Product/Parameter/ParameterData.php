<?php

namespace SS6\ShopBundle\Model\Product\Parameter;

use SS6\ShopBundle\Model\Product\Parameter\Parameter;

class ParameterData {

	/**
	 * @var string[]
	 */
	public $name;

	/**
	 * @param string[] $name
	 */
	public function __construct(array $name = []) {
		$this->name = $name;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Parameter\Parameter $parameter
	 */
	public function setFromEntity(Parameter $parameter) {
		$translations = $parameter->getTranslations();
		$names = [];
		foreach ($translations as $translate) {
			$names[$translate->getLocale()] = $translate->getName();
		}
		$this->name = $names;
	}

}
