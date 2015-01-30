<?php

namespace SS6\ShopBundle\Model\AdvanceSearch\Filter;

use SS6\ShopBundle\Model\AdvanceSearch\AdvanceSearchFilterInterface;

class ProductNameFilter implements AdvanceSearchFilterInterface {

	/**
	 * {@inheritdoc}
	 */
	public function getName() {
		return 'productName';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAllowedOperators() {
		return [
			self::OPERATOR_CONTAIN,
			self::OPERATOR_NOT_CONTAIN,
			self::OPERATOR_IS,
			self::OPERATOR_IS_NOT,
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getValueFormType() {
		return 'text';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getValueFormOptions() {
		return [];
	}

}
