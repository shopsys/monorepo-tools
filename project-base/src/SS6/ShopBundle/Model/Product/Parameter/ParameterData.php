<?php

namespace SS6\ShopBundle\Model\Product\Parameter;

use SS6\ShopBundle\Model\Product\Parameter\Parameter;

class ParameterData {

	/**
	 * @var string[]
	 */
	private $name;

	/**
	 * @param string[] $name
	 */
	public function __construct(array $name = []) {
		$this->name = $name;
	}

	/**
	 * @return string[]
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string[] $name
	 */
	public function setName(array $name) {
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
		$this->setName($names);
	}

}
