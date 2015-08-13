<?php

namespace SS6\ShopBundle\Twig;

use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Model\Product\Product;
use Twig_SimpleFilter;

class ProductExtension extends \Twig_Extension {

	/**
	 * @var \Symfony\Component\Translation\Translator
	 */
	private $translator;

	public function __construct(Translator $translator) {
		$this->translator = $translator;
	}

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
			return $this->translator->trans('ID %productId%', [
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
			return $this->translator->trans('Český název zboží není vyplněn');
		}

		return $product->getName();
	}

}
