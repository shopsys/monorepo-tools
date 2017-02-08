<?php

namespace SS6\ShopBundle\Model\Script;

use SS6\ShopBundle\Model\Script\Script;

class ScriptData {

	/**
	 * @var string|null
	 */
	public $name;

	/**
	 * @var string|null
	 */
	public $code;

	/**
	 * @var string|null
	 */
	public $placement;

	/**
	 * @param \SS6\ShopBundle\Model\Script\Script $script
	 */
	public function setFromEntity(Script $script) {
		$this->name = $script->getName();
		$this->code = $script->getCode();
		$this->placement = $script->getPlacement();
	}
}
