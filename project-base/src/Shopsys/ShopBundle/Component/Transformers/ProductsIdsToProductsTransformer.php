<?php

namespace Shopsys\FrameworkBundle\Component\Transformers;

use IteratorAggregate;
use Shopsys\FrameworkBundle\Model\Product\ProductRepository;
use Symfony\Component\Form\DataTransformerInterface;

class ProductsIdsToProductsTransformer implements DataTransformerInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductRepository
     */
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product[]|null $products
     * @return int[]
     */
    public function transform($products)
    {
        $productsIds = [];

        if (is_array($products) || $products instanceof IteratorAggregate) {
            foreach ($products as $key => $product) {
                $productsIds[$key] = $product->getId();
            }
        }

        return $productsIds;
    }

    /**
     * @param int[] $productsIds
     * @return \Shopsys\FrameworkBundle\Model\Product\Product[]|null
     */
    public function reverseTransform($productsIds)
    {
        $products = [];

        if (is_array($productsIds)) {
            foreach ($productsIds as $key => $productId) {
                try {
                    $products[$key] = $this->productRepository->getById($productId);
                } catch (\Shopsys\FrameworkBundle\Model\Product\Exception\ProductNotFoundException $e) {
                    throw new \Symfony\Component\Form\Exception\TransformationFailedException('Product not found', null, $e);
                }
            }
        }

        return $products;
    }
}
