<?php

namespace SS6\ShopBundle\Model\Product\Flag;

class FlagData {

	/**
	 * @var string[]
	 */
	public $name;

	/**
	 * @var string
	 */
	public $rgbColor;

	/**
	 * @param array $name
	 * @param string $rgbColor
	 */
	public function __construct(array $name = [], $rgbColor = null) {
		$this->name = $name;
		$this->rgbColor = $rgbColor;
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
		$this->rgbColor = $flag->getRgbColor();
	}

}
