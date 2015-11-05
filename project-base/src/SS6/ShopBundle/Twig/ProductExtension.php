<?php

namespace SS6\ShopBundle\Twig;

use SS6\ShopBundle\Model\Product\Product;
use Twig_SimpleFilter;

class ProductExtension extends \Twig_Extension {

	/**
	 * @return array
	 */
	public function getFilters() {
		return [
			new Twig_SimpleFilter('productDisplayName', [$this, 'getProductDisplayName']),
			new Twig_SimpleFilter('productListDisplayName', [$this, 'getProductListDisplayName']),
		];
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'product';
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return string
	 */
	public function getProductDisplayName(Product $product) {
		if ($product->getName() === null) {
			return t('ID %productId%', [
				'%productId%' => $product->getId(),
			]);
		}

		return $product->getName();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return string
	 */
	public function getProductListDisplayName(Product $product) {
		if ($product->getName() === null) {
			return t('Český název zboží není vyplněn');
		}

		return $product->getName();
	}

}
