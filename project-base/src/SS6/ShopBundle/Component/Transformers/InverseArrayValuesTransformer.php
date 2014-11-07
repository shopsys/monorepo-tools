<?php

namespace SS6\ShopBundle\Component\Transformers;

use Symfony\Component\Form\DataTransformerInterface;

class InverseArrayValuesTransformer implements DataTransformerInterface {

	/**
	 * @param array $value
	 * @return array
	 */
	public function transform($value) {
		return $this->inverseDomainsInArray($value);
	}

	/**
	 * @param array $value
	 * @return array
	 */
	public function reverseTransform($value) {
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
