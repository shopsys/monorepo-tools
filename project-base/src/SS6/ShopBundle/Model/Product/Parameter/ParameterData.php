<?php

namespace SS6\ShopBundle\Model\Product\Parameter;

use SS6\ShopBundle\Model\Product\Parameter\Parameter;

class ParameterData {

	/**
	 * @var array
	 */
	private $names;

	/**
	 * @param array $names
	 */
	public function __construct($names = array()) {
		$this->names = $names;
	}

	/**
	 * @return array
	 */
	public function getNames() {
		return $this->names;
	}

	/**
	 * @param array $names
	 */
	public function setNames($names) {
		$this->names = $names;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Parameter\Parameter $parameter
	 */
	public function setFromEntity(Parameter $parameter) {
		$translations = $parameter->getTranslations();
		$names = array();
		foreach ($translations as $translate) {
			$names[$translate->getLocale()] = $translate->getName();
		}
		$this->setNames($names);
	}

}
