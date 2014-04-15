<?php

namespace SS6\ShopBundle\Model\Product;

use SS6\ShopBundle\Exception\ValidationException;
use SS6\ShopBundle\Model\Product\Product;
use Symfony\Component\Validator\Validator;

class ProductEditService {
	
	/**
	 * @var Validator
	 */
	private $validator;
	
	/**
	 * @param Validator $validator
	 */
	public function __construct(Validator $validator) {
		$this->validator = $validator;
	}
	
	/**
	 * @param Product $product
	 * @return Product
	 * @throws ValidationException
	 */
	public function edit(Product $product) {
		$constraintViolations = $this->validator->validate($product);
		
		if ($constraintViolations->count() > 0) {
			throw new ValidationException($constraintViolations);
		}
		
		return $product;
	}

}
