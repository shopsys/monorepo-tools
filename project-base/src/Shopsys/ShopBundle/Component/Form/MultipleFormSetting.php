<?php

namespace Shopsys\ShopBundle\Component\Form;

class MultipleFormSetting {

	const DEFAULT_MULTIPLE = false;

	/**
	 * @var bool
	 */
	private $isCurrentFormMultiple = self::DEFAULT_MULTIPLE;

	public function currentFormIsMultiple() {
		$this->isCurrentFormMultiple = true;
	}

	public function currentFormIsNotMultiple() {
		$this->isCurrentFormMultiple = false;
	}

	public function reset() {
		$this->isCurrentFormMultiple = self::DEFAULT_MULTIPLE;
	}

	/**
	 * @return bool
	 */
	public function isCurrentFormMultiple() {
		return $this->isCurrentFormMultiple;
	}

}
