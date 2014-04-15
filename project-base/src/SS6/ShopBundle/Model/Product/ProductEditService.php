<?php

namespace SS6\ShopBundle\Model\Product;

use SS6\ShopBundle\Model\Product\Product;
use Symfony\Component\Validator\Validator;

class ProductEditService {
	
	/**
	 * @var \Symfony\Component\Validator\Validator
	 */
	private $validator;
	
	/**
	 * @param \Symfony\Component\Validator\Validator $validator
	 */
	public function __construct(Validator $validator) {
		$this->validator = $validator;
	}
	
	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return \SS6\ShopBundle\Model\Product\Product
	 * @throws \SS6\ShopBundle\Exception\ValidationException
	 */
	public function edit(Product $product) {
		$constraintViolations = $this->validator->validate($product);
		
		if ($constraintViolations->count() > 0) {
			throw new \SS6\ShopBundle\Exception\ValidationException($constraintViolations);
		}
		
		return $product;
	}

}
