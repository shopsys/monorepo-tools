<?php

namespace SS6\ShopBundle\Model\AdvanceSearch;

interface AdvanceSearchFilterInterface {

	const OPERATOR_CONTAIN = 'contain';
	const OPERATOR_NOT_CONTAIN = 'notContain';
	const OPERATOR_IS = 'is';
	const OPERATOR_IS_NOT = 'isNot';

	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @return string[]
	 */
	public function getAllowedOperators();

	/**
	 * @return string|FormTypeInterface
	 */
	public function getValueFormType();

	/**
	 * @return array
	 */
	public function getValueFormOptions();

}