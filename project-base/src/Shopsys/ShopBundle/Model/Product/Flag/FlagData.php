<?php

namespace Shopsys\ShopBundle\Model\Product\Flag;

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
	 * @var bool
	 */
	public $visible;

	/**
	 * @param array $name
	 * @param string $rgbColor
	 * @param bool $visible
	 */
	public function __construct(array $name = [], $rgbColor = null, $visible = false) {
		$this->name = $name;
		$this->rgbColor = $rgbColor;
		$this->visible = $visible;
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Product\Flag\Flag $flag
	 */
	public function setFromEntity(Flag $flag) {
		$translations = $flag->getTranslations();
		$names = [];
		foreach ($translations as $translate) {
			$names[$translate->getLocale()] = $translate->getName();
		}
		$this->name = $names;
		$this->rgbColor = $flag->getRgbColor();
		$this->visible = $flag->isVisible();
	}

}
