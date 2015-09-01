<?php

namespace SS6\ShopBundle\Component\Transformers;

use Symfony\Component\Form\DataTransformerInterface;

class InverseArrayValuesTransformer implements DataTransformerInterface {

	/**
	 * {@inheritDoc}
	 */
	public function transform($value) {
		if (!is_array($value)) {
			return '';
		}

		return $this->inverseDomainsInArray($value);
	}

	/**
	 * {@inheritDoc}
	 */
	public function reverseTransform($value) {
		if (!is_array($value)) {
			return null;
		}

		return $this->inverseDomainsInArray($value);
	}

	/**
	 * @param array $items
	 * @return array
	 */
	private function inverseDomainsInArray($items) {
		foreach ($items as $key => $value) {
			$items[$key] = !$value;
		}
		return $items;
	}
}
