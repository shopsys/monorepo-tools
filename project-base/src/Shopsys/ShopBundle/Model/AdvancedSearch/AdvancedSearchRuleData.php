<?php

namespace SS6\ShopBundle\Model\AdvancedSearch;

class AdvancedSearchRuleData {

	/**
	 * @var string|null
	 */
	public $subject;

	/**
	 * @var string|null
	 */
	public $operator;

	/**
	 * @var mixed
	 */
	public $value;

	/**
	 * @param string|null $subject
	 * @param string|null $operator
	 * @param mixed $value
	 */
	public function __construct($subject = null, $operator = null, $value = null) {
		$this->subject = $subject;
		$this->operator = $operator;
		$this->value = $value;
	}

}
