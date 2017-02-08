<?php

namespace SS6\ShopBundle\Form\Admin\Product\TopProduct;

use SS6\ShopBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer;
use SS6\ShopBundle\Form\Admin\Product\TopProduct\TopProductsFormType;

class TopProductsFormTypeFactory {

	/**
	 * @var \SS6\ShopBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer
	 */
	private $removeDuplicatesTransformer;

	/**
	 * @param \SS6\ShopBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer $removeDuplicatesTransformer
	 */
	public function __construct(RemoveDuplicatesFromArrayTransformer $removeDuplicatesTransformer) {
		$this->removeDuplicatesTransformer = $removeDuplicatesTransformer;
	}

	/**
	 * @return \SS6\ShopBundle\Form\Admin\Product\TopProduct\TopProductsFormType
	 */
	public function create() {
		return new TopProductsFormType($this->removeDuplicatesTransformer);
	}

}
