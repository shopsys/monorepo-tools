<?php

namespace Shopsys\FrameworkBundle\Component\Transformers;

use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductRepository;
use Symfony\Component\Form\DataTransformerInterface;

class ProductIdToProductTransformer implements DataTransformerInterface
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
     * @param \Shopsys\FrameworkBundle\Model\Product\Product|null $product
     * @return int|null
     */
    public function transform($product)
    {
        if ($product instanceof Product) {
            return $product->getId();
        }
        return null;
    }

    /**
     * @param int $productId
     * @return \Shopsys\FrameworkBundle\Model\Product\Product|null
     */
    public function reverseTransform($productId)
    {
        if (empty($productId)) {
            return null;
        }
        try {
            $product = $this->productRepository->getById($productId);
        } catch (\Shopsys\FrameworkBundle\Model\Product\Exception\ProductNotFoundException $e) {
            throw new \Symfony\Component\Form\Exception\TransformationFailedException('Product not found', null, $e);
        }
        return $product;
    }
}
