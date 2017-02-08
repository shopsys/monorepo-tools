<?php

namespace Shopsys\ShopBundle\Component\Transformers;

use IteratorAggregate;
use Shopsys\ShopBundle\Model\Product\ProductRepository;
use Symfony\Component\Form\DataTransformerInterface;

class ProductsIdsToProductsTransformer implements DataTransformerInterface {

	/**
	 * @var \Shopsys\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	public function __construct(ProductRepository $productRepository) {
		$this->productRepository = $productRepository;
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Product\Product[]|null $products
	 * @return int[]
	 */
	public function transform($products) {
		$productsIds = [];

		if (is_array($products) || $products instanceof IteratorAggregate) {
			foreach ($products as $product) {
				$productsIds[] = $product->getId();
			}
		}

		return $productsIds;
	}

	/**
	 * @param int[] $productsIds
	 * @return \Shopsys\ShopBundle\Model\Product\Product[]|null
	 */
	public function reverseTransform($productsIds) {
		$products = [];

		if (is_array($productsIds)) {
			foreach ($productsIds as $productId) {
				try {
					$products[] = $this->productRepository->getById($productId);
				} catch (\Shopsys\ShopBundle\Model\Product\Exception\ProductNotFoundException $e) {
					throw new \Symfony\Component\Form\Exception\TransformationFailedException('Product not found', null, $e);
				}
			}
		}

		return $products;
	}
}
