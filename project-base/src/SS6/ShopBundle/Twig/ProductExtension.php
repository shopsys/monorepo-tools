<?php

namespace SS6\ShopBundle\Twig;

use SS6\ShopBundle\Model\Product\ProductEditFacade;
use Twig_Extension;
use Twig_SimpleFunction;

class ProductExtension extends Twig_Extension {

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductEditFacade
	 */
	private $productEditFacade;

	public function __construct(ProductEditFacade $productEditFacade) {
		$this->productEditFacade = $productEditFacade;
	}

	/**
	 * @return array
	 */
	public function getFunctions() {
		return [
			new Twig_SimpleFunction('getProductNameOrNullById', [$this, 'getProductNameOrNullById']),
		];
	}

	/**
	 * @param int $productId
	 * @param string|null $locale
	 * @return string
	 */
	public function getProductNameOrNullById($productId, $locale = null) {
		try {
			$product = $this->productEditFacade->getById((int)$productId);
			return $product->getName($locale);
		} catch (\SS6\ShopBundle\Model\Product\Exception\ProductNotFoundException $ex) {
			return null;
		}
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'product_extension';
	}
}
