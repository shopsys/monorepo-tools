<?php

namespace SS6\ShopBundle\Model\Product\Flag;

class FlagData {

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
	 * @param \SS6\ShopBundle\Model\Product\Flag\Flag $flag
	 */
	public function setFromEntity(Flag $flag) {
		$translations = $flag->getTranslations();
		$names = [];
		foreach ($translations as $translate) {
			$names[$translate->getLocale()] = $translate->getName();
		}
		$this->name = $names;
	}

}
