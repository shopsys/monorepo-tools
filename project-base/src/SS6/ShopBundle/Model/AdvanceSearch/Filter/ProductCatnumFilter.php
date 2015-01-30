<?php

namespace SS6\ShopBundle\Model\AdvanceSearch\Filter;

use SS6\ShopBundle\Model\AdvanceSearch\AdvanceSearchFilterInterface;

class ProductCatnumFilter implements AdvanceSearchFilterInterface {

	/**
	 * {@inheritdoc}
	 */
	public function getName() {
		return 'productCatnum';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAllowedOperators() {
		return [
			self::OPERATOR_IS,
			self::OPERATOR_NOT_CONTAIN,
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
